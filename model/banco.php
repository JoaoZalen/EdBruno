<?php

class banco
{
    private string $host = "localhost";
    private string $login = "root";
    private string $senha = "";
    private string $dataBase = "ed_ensino";

    public function conectarBD(): mysqli
    {
        $conexao = mysqli_connect($this->host, $this->login, $this->senha, $this->dataBase);
        if (!$conexao) {
            die("Erro ao conectar ao banco de dados.");
        }
        mysqli_set_charset($conexao, "utf8mb4");
        return $conexao;
    }

    public function getUserByEmail(string $email): ?array
    {
        $conexao = $this->conectarBD();
        $stmt = mysqli_prepare($conexao, "SELECT * FROM usuarios WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($resultado);
        return $user ?: null;
    }

    public function getUserById(int $id): ?array
    {
        $conexao = $this->conectarBD();
        $stmt = mysqli_prepare($conexao, "SELECT * FROM usuarios WHERE id = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($resultado);
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

        if (hash_equals($user['senha'], $senha)) {
            $this->atualizarSenha((int)$user['id'], $senha);
            return $this->getUserByEmail($email);
        }

        return null;
    }

    public function inserirUsuario($usuario): bool
    {
        $conexao = $this->conectarBD();
        $senhaHash = password_hash($usuario->get_Senha(), PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conexao, "INSERT INTO usuarios (nome, email, cpf, senha, foto) VALUES (?, ?, ?, ?, ?)");
        $nome = $usuario->get_Nome();
        $email = $usuario->get_Email();
        $cpf = preg_replace('/\D/', '', $usuario->get_Cpf());
        $foto = $usuario->get_Foto();
        mysqli_stmt_bind_param($stmt, "sssss", $nome, $email, $cpf, $senhaHash, $foto);
        return mysqli_stmt_execute($stmt);
    }

    public function atualizarPerfil(int $id, string $nome, string $email, string $cpf): bool
    {
        $conexao = $this->conectarBD();
        $cpfLimpo = preg_replace('/\D/', '', $cpf);
        $stmt = mysqli_prepare($conexao, "UPDATE usuarios SET nome = ?, email = ?, cpf = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "sssi", $nome, $email, $cpfLimpo, $id);
        return mysqli_stmt_execute($stmt);
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
        $conexao = $this->conectarBD();
        $stmt = mysqli_prepare($conexao, "INSERT INTO recuperacao_senha (usuario_id, token_hash, expira_em) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))");
        $id = (int)$user['id'];
        mysqli_stmt_bind_param($stmt, "is", $id, $hash);
        mysqli_stmt_execute($stmt);
        return $token;
    }

    public function redefinirSenhaPorToken(string $token, string $senha): bool
    {
        $hash = hash('sha256', $token);
        $conexao = $this->conectarBD();
        $stmt = mysqli_prepare($conexao, "SELECT * FROM recuperacao_senha WHERE token_hash = ? AND usado_em IS NULL AND expira_em >= NOW() LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $hash);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        if (!$row) {
            return false;
        }
        $this->atualizarSenha((int)$row['usuario_id'], $senha);
        $stmt = mysqli_prepare($conexao, "UPDATE recuperacao_senha SET usado_em = NOW() WHERE id = ?");
        $id = (int)$row['id'];
        mysqli_stmt_bind_param($stmt, "i", $id);
        return mysqli_stmt_execute($stmt);
    }

    public function registrarPartida(int $usuarioId, int $acertos, int $total, int $moedas): void
    {
        $conexao = $this->conectarBD();
        mysqli_begin_transaction($conexao);
        $stmt = mysqli_prepare($conexao, "INSERT INTO quiz_partidas (usuario_id, acertos, total_perguntas, moedas_ganhas) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iiii", $usuarioId, $acertos, $total, $moedas);
        mysqli_stmt_execute($stmt);
        $partidaId = mysqli_insert_id($conexao);
        $stmt = mysqli_prepare($conexao, "UPDATE usuarios SET moedas = moedas + ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $moedas, $usuarioId);
        mysqli_stmt_execute($stmt);
        $tipo = "credito";
        $motivo = "Recompensa do Quiz #" . $partidaId;
        $stmt = mysqli_prepare($conexao, "INSERT INTO moedas_movimentacoes (usuario_id, valor, tipo, motivo) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iiss", $usuarioId, $moedas, $tipo, $motivo);
        mysqli_stmt_execute($stmt);
        mysqli_commit($conexao);
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
        $stmt = mysqli_prepare($conexao, "SELECT * FROM itens WHERE id = ? FOR UPDATE");
        mysqli_stmt_bind_param($stmt, "i", $itemId);
        mysqli_stmt_execute($stmt);
        $item = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        $user = $this->getUserById($usuarioId);

        $stmt = mysqli_prepare($conexao, "SELECT id FROM inventario WHERE usuario_id = ? AND item_id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $usuarioId, $itemId);
        mysqli_stmt_execute($stmt);
        $jaTem = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if (!$item || $jaTem) {
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
    }

    public function equiparItem(int $usuarioId, int $itemId): bool
    {
        $conexao = $this->conectarBD();
        $stmt = mysqli_prepare($conexao, "SELECT i.categoria FROM inventario inv JOIN itens i ON i.id = inv.item_id WHERE inv.usuario_id = ? AND i.id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $usuarioId, $itemId);
        mysqli_stmt_execute($stmt);
        $item = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        if (!$item) {
            return false;
        }
        $categoria = $item['categoria'];
        $stmt = mysqli_prepare($conexao, "UPDATE inventario inv JOIN itens i ON i.id = inv.item_id SET inv.equipado = 0 WHERE inv.usuario_id = ? AND i.categoria = ?");
        mysqli_stmt_bind_param($stmt, "is", $usuarioId, $categoria);
        mysqli_stmt_execute($stmt);
        $stmt = mysqli_prepare($conexao, "UPDATE inventario SET equipado = 1 WHERE usuario_id = ? AND item_id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $usuarioId, $itemId);
        return mysqli_stmt_execute($stmt);
    }
}
?>
