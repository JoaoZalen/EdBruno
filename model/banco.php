<?php

require_once __DIR__ . '/avatar_itens.php';

class banco
{
    private string $host = "localhost";
    private string $login = "root";
    private string $senha = "";
    private string $dataBase = "ed_ensino";

    /** Código do MySQL para "valor duplicado" em chave UNIQUE (e-mail/CPF). */
    private const ERRO_DUPLICADO = 1062;

    /** Uma única conexão por requisição, reaproveitada por todos os métodos. */
    private static ?mysqli $conexao = null;

    public function conectarBD(): mysqli
    {
        if (self::$conexao !== null) {
            return self::$conexao;
        }

        // Erros do mysqli viram exceções (padrão do PHP 8.1+, deixado explícito)
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            $conexao = mysqli_connect($this->host, $this->login, $this->senha, $this->dataBase);
        } catch (mysqli_sql_exception $e) {
            error_log("Erro ao conectar ao banco: " . $e->getMessage());
            die("Erro ao conectar ao banco de dados. Verifique se o MySQL está ligado no XAMPP e se o arquivo ed_ensino.sql foi importado.");
        }

        mysqli_set_charset($conexao, "utf8mb4");
        self::$conexao = $conexao;
        return $conexao;
    }

    public function getUserByEmail(string $email): ?array
    {
        $conexao = $this->conectarBD();
        $stmt = mysqli_prepare($conexao, "SELECT * FROM usuarios WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        return $user ?: null;
    }

    public function getUserById(int $id): ?array
    {
        $conexao = $this->conectarBD();
        $stmt = mysqli_prepare($conexao, "SELECT * FROM usuarios WHERE id = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        return $user ?: null;
    }

    public function autenticar(string $email, string $senha): ?array
    {
        $user = $this->getUserByEmail($email);
        if (!$user) {
            return null;
        }

        if (password_verify($senha, $user['senha'])) {
            return $user;
        }

        // Usuários antigos com senha salva em texto puro: confere e já converte
        // para hash. Só vale se o que está no banco NÃO for um hash — senão daria
        // para entrar digitando o próprio hash como senha.
        $ehHash = password_get_info($user['senha'])['algo'] !== null;
        if (!$ehHash && hash_equals($user['senha'], $senha)) {
            $this->atualizarSenha((int)$user['id'], $senha);
            return $this->getUserByEmail($email);
        }

        return null;
    }

    /** Devolve false se o e-mail ou o CPF já estiverem cadastrados. */
    public function inserirUsuario($usuario): bool
    {
        $conexao = $this->conectarBD();
        $senhaHash = password_hash($usuario->get_Senha(), PASSWORD_DEFAULT);
        $nome = $usuario->get_Nome();
        $email = $usuario->get_Email();
        $cpf = preg_replace('/\D/', '', $usuario->get_Cpf());
        $foto = $usuario->get_Foto();

        try {
            $stmt = mysqli_prepare($conexao, "INSERT INTO usuarios (nome, email, cpf, senha, foto) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sssss", $nome, $email, $cpf, $senhaHash, $foto);
            return mysqli_stmt_execute($stmt);
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === self::ERRO_DUPLICADO) {
                return false;
            }
            throw $e;
        }
    }

    /** Devolve false se o novo e-mail ou CPF já pertencerem a outro usuário. */
    public function atualizarPerfil(int $id, string $nome, string $email, string $cpf): bool
    {
        $conexao = $this->conectarBD();
        $cpfLimpo = preg_replace('/\D/', '', $cpf);

        try {
            $stmt = mysqli_prepare($conexao, "UPDATE usuarios SET nome = ?, email = ?, cpf = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "sssi", $nome, $email, $cpfLimpo, $id);
            return mysqli_stmt_execute($stmt);
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === self::ERRO_DUPLICADO) {
                return false;
            }
            throw $e;
        }
    }

    public function atualizarSenha(int $id, string $senha): bool
    {
        $conexao = $this->conectarBD();
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conexao, "UPDATE usuarios SET senha = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $hash, $id);
        return mysqli_stmt_execute($stmt);
    }

    public function criarTokenRecuperacao(string $email): ?string
    {
        $user = $this->getUserByEmail($email);
        if (!$user) {
            return null;
        }
        $token = bin2hex(random_bytes(24));
        $hash = hash('sha256', $token);
        $id = (int)$user['id'];
        $conexao = $this->conectarBD();

        // invalida tokens antigos ainda não usados desse usuário
        $stmt = mysqli_prepare($conexao, "UPDATE recuperacao_senha SET usado_em = NOW() WHERE usuario_id = ? AND usado_em IS NULL");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $stmt = mysqli_prepare($conexao, "INSERT INTO recuperacao_senha (usuario_id, token_hash, expira_em) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))");
        mysqli_stmt_bind_param($stmt, "is", $id, $hash);
        mysqli_stmt_execute($stmt);
        return $token;
    }

    public function redefinirSenhaPorToken(string $token, string $senha): bool
    {
        if ($token === '') {
            return false;
        }
        $hash = hash('sha256', $token);
        $conexao = $this->conectarBD();
        mysqli_begin_transaction($conexao);
        try {
            $stmt = mysqli_prepare($conexao, "SELECT * FROM recuperacao_senha WHERE token_hash = ? AND usado_em IS NULL AND expira_em >= NOW() LIMIT 1 FOR UPDATE");
            mysqli_stmt_bind_param($stmt, "s", $hash);
            mysqli_stmt_execute($stmt);
            $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            if (!$row) {
                mysqli_rollback($conexao);
                return false;
            }

            $this->atualizarSenha((int)$row['usuario_id'], $senha);

            $stmt = mysqli_prepare($conexao, "UPDATE recuperacao_senha SET usado_em = NOW() WHERE id = ?");
            $id = (int)$row['id'];
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            mysqli_commit($conexao);
            return true;
        } catch (Throwable $e) {
            mysqli_rollback($conexao);
            throw $e;
        }
    }

    public function registrarPartida(int $usuarioId, int $acertos, int $total, int $moedas): void
    {
        $conexao = $this->conectarBD();
        mysqli_begin_transaction($conexao);
        try {
            $stmt = mysqli_prepare($conexao, "INSERT INTO quiz_partidas (usuario_id, acertos, total_perguntas, moedas_ganhas) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "iiii", $usuarioId, $acertos, $total, $moedas);
            mysqli_stmt_execute($stmt);
            $partidaId = mysqli_insert_id($conexao);

            if ($moedas > 0) {
                $stmt = mysqli_prepare($conexao, "UPDATE usuarios SET moedas = moedas + ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "ii", $moedas, $usuarioId);
                mysqli_stmt_execute($stmt);

                $tipo = "credito";
                $motivo = "Recompensa do Quiz #" . $partidaId;
                $stmt = mysqli_prepare($conexao, "INSERT INTO moedas_movimentacoes (usuario_id, valor, tipo, motivo) VALUES (?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "iiss", $usuarioId, $moedas, $tipo, $motivo);
                mysqli_stmt_execute($stmt);
            }

            mysqli_commit($conexao);
        } catch (Throwable $e) {
            mysqli_rollback($conexao);
            throw $e;
        }
    }

    public function listarItens(): array
    {
        $resultado = mysqli_query($this->conectarBD(), "SELECT * FROM itens ORDER BY categoria, preco");
        return mysqli_fetch_all($resultado, MYSQLI_ASSOC);
    }

    public function listarInventario(int $usuarioId): array
    {
        $conexao = $this->conectarBD();
        $stmt = mysqli_prepare($conexao, "SELECT i.*, inv.equipado FROM inventario inv JOIN itens i ON i.id = inv.item_id WHERE inv.usuario_id = ? ORDER BY i.categoria, i.nome");
        mysqli_stmt_bind_param($stmt, "i", $usuarioId);
        mysqli_stmt_execute($stmt);
        return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    }

    public function listarItensEquipados(int $usuarioId): array
    {
        $conexao = $this->conectarBD();
        $stmt = mysqli_prepare($conexao, "SELECT i.* FROM inventario inv JOIN itens i ON i.id = inv.item_id WHERE inv.usuario_id = ? AND inv.equipado = 1 ORDER BY i.categoria, i.nome");
        mysqli_stmt_bind_param($stmt, "i", $usuarioId);
        mysqli_stmt_execute($stmt);
        return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    }

    public function comprarItem(int $usuarioId, int $itemId): string
    {
        $conexao = $this->conectarBD();
        mysqli_begin_transaction($conexao);
        try {
            // trava o saldo do usuário até o fim da compra (evita gastar a mesma moeda duas vezes)
            $stmt = mysqli_prepare($conexao, "SELECT moedas FROM usuarios WHERE id = ? FOR UPDATE");
            mysqli_stmt_bind_param($stmt, "i", $usuarioId);
            mysqli_stmt_execute($stmt);
            $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

            $stmt = mysqli_prepare($conexao, "SELECT * FROM itens WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $itemId);
            mysqli_stmt_execute($stmt);
            $item = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

            $stmt = mysqli_prepare($conexao, "SELECT id FROM inventario WHERE usuario_id = ? AND item_id = ?");
            mysqli_stmt_bind_param($stmt, "ii", $usuarioId, $itemId);
            mysqli_stmt_execute($stmt);
            $jaTem = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

            if (!$user || !$item || $jaTem) {
                mysqli_rollback($conexao);
                return "Item indisponível ou já comprado.";
            }
            if ((int)$user['moedas'] < (int)$item['preco']) {
                mysqli_rollback($conexao);
                return "Moedas insuficientes.";
            }

            $preco = (int)$item['preco'];
            $stmt = mysqli_prepare($conexao, "UPDATE usuarios SET moedas = moedas - ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "ii", $preco, $usuarioId);
            mysqli_stmt_execute($stmt);

            $stmt = mysqli_prepare($conexao, "INSERT INTO inventario (usuario_id, item_id) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "ii", $usuarioId, $itemId);
            mysqli_stmt_execute($stmt);

            $valor = -$preco;
            $tipo = "debito";
            $motivo = "Compra: " . $item['nome'];
            $stmt = mysqli_prepare($conexao, "INSERT INTO moedas_movimentacoes (usuario_id, valor, tipo, motivo) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "iiss", $usuarioId, $valor, $tipo, $motivo);
            mysqli_stmt_execute($stmt);

            mysqli_commit($conexao);
            return "Item comprado com sucesso.";
        } catch (mysqli_sql_exception $e) {
            mysqli_rollback($conexao);
            error_log("Erro na compra: " . $e->getMessage());
            return "Não foi possível concluir a compra. Tente novamente.";
        }
    }

    /**
     * Equipa um item do inventário.
     * Só pode haver um item por SLOT do corpo (chapéu, rosto, pés...):
     * o item que estava no mesmo slot é desequipado automaticamente.
     * Devolve o item equipado ou null se o usuário não tiver o item.
     */
    public function equiparItem(int $usuarioId, int $itemId): ?array
    {
        $conexao = $this->conectarBD();
        $stmt = mysqli_prepare($conexao, "SELECT i.* FROM inventario inv JOIN itens i ON i.id = inv.item_id WHERE inv.usuario_id = ? AND i.id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $usuarioId, $itemId);
        mysqli_stmt_execute($stmt);
        $item = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        if (!$item) {
            return null;
        }

        $slot = slotDoItem($item);
        mysqli_begin_transaction($conexao);
        try {
            // tira o item que ocupava o mesmo slot
            foreach ($this->listarItensEquipados($usuarioId) as $equipado) {
                if (slotDoItem($equipado) === $slot) {
                    $idAntigo = (int)$equipado['id'];
                    $stmt = mysqli_prepare($conexao, "UPDATE inventario SET equipado = 0 WHERE usuario_id = ? AND item_id = ?");
                    mysqli_stmt_bind_param($stmt, "ii", $usuarioId, $idAntigo);
                    mysqli_stmt_execute($stmt);
                }
            }

            $stmt = mysqli_prepare($conexao, "UPDATE inventario SET equipado = 1 WHERE usuario_id = ? AND item_id = ?");
            mysqli_stmt_bind_param($stmt, "ii", $usuarioId, $itemId);
            mysqli_stmt_execute($stmt);
            mysqli_commit($conexao);
            return $item;
        } catch (Throwable $e) {
            mysqli_rollback($conexao);
            throw $e;
        }
    }

    /** Remove um item do avatar (ele continua no inventário). */
    public function desequiparItem(int $usuarioId, int $itemId): bool
    {
        $conexao = $this->conectarBD();
        $stmt = mysqli_prepare($conexao, "UPDATE inventario SET equipado = 0 WHERE usuario_id = ? AND item_id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $usuarioId, $itemId);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_affected_rows($stmt) > 0;
    }
}
?>
