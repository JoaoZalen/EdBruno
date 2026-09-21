<?php
session_start();
require_once("../model/banco.php");
require_once("../model/usuario.php");

$banco = new banco();
$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';

function voltarComErro(string $pagina, string $mensagem): void
{
    $_SESSION['Error'] = $mensagem;
    header("Location:../view/" . $pagina);
    exit;
}

function criarSessaoUsuario(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['estaLogado'] = true;
    $_SESSION['usuario_id'] = (int)$user['id'];
    $_SESSION['usuario'] = new Usuario($user['nome'], $user['email'], $user['cpf'], $user['senha']);
    $_SESSION['usuario']->set_Foto($user['foto'] ?: 'default.png');
}

if ($acao === 'login') {
    $email = trim($_POST['inputEmail'] ?? '');
    $senha = $_POST['inputSenha'] ?? '';
    if ($email === '' || $senha === '') {
        voltarComErro('login.php', 'Preencha e-mail e senha.');
    }
    $user = $banco->autenticar($email, $senha);
    if (!$user) {
        voltarComErro('login.php', 'Erro ao fazer login. Verifique suas credenciais.');
    }
    criarSessaoUsuario($user);
    header('Location:../view/home.php');
    exit;
}

if ($acao === 'cadastro') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $cpf = trim($_POST['cpf'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    if ($nome === '' || $email === '' || $cpf === '' || $senha === '') {
        voltarComErro('cadastro.php', 'Preencha todos os campos.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        voltarComErro('cadastro.php', 'Informe um e-mail válido.');
    }
    if ($senha !== $confirmarSenha) {
        voltarComErro('cadastro.php', 'As senhas não coincidem.');
    }

    $usuario = new Usuario($nome, $email, $cpf, $senha);
    if (!$banco->inserirUsuario($usuario)) {
        voltarComErro('cadastro.php', 'Não foi possível cadastrar. Verifique e-mail ou CPF duplicado.');
    }
    $user = $banco->autenticar($email, $senha);
    criarSessaoUsuario($user);
    header('Location:../view/home.php');
    exit;
}

if ($acao === 'recuperar_senha') {
    $email = trim($_POST['email'] ?? '');
    $token = $email !== '' ? $banco->criarTokenRecuperacao($email) : null;
    if ($token) {
        $_SESSION['LinkRecuperacao'] = 'redefinir_senha.php?token=' . urlencode($token);
    }
    $_SESSION['Sucesso'] = 'Se o e-mail estiver cadastrado, as instruções serão geradas.';
    header('Location:../view/recuperar_senha.php');
    exit;
}

if ($acao === 'redefinir_senha') {
    $token = $_POST['token'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $confirmar = $_POST['confirmar_senha'] ?? '';
    if (strlen($senha) < 6 || $senha !== $confirmar || !$banco->redefinirSenhaPorToken($token, $senha)) {
        $_SESSION['Error'] = 'Token inválido ou senha incorreta.';
        header('Location:../view/redefinir_senha.php?token=' . urlencode($token));
        exit;
    }
    $_SESSION['Sucesso'] = 'Senha redefinida. Faça login.';
    header('Location:../view/login.php');
    exit;
}

if (!isset($_SESSION['estaLogado']) || $_SESSION['estaLogado'] !== true) {
    header('Location:../view/login.php');
    exit;
}

$usuarioId = (int)$_SESSION['usuario_id'];

if ($acao === 'perfil') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $cpf = trim($_POST['cpf'] ?? '');
    if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $cpf === '') {
        voltarComErro('perfil.php', 'Confira os dados do perfil.');
    }
    $banco->atualizarPerfil($usuarioId, $nome, $email, $cpf);
    criarSessaoUsuario($banco->getUserById($usuarioId));
    $_SESSION['Sucesso'] = 'Perfil atualizado.';
    header('Location:../view/perfil.php');
    exit;
}

if ($acao === 'trocar_senha') {
    $senha = $_POST['senha'] ?? '';
    $confirmar = $_POST['confirmar_senha'] ?? '';
    if (strlen($senha) < 6 || $senha !== $confirmar) {
        voltarComErro('perfil.php', 'A senha precisa ter 6 caracteres e confirmação igual.');
    }
    $banco->atualizarSenha($usuarioId, $senha);
    $_SESSION['Sucesso'] = 'Senha alterada.';
    header('Location:../view/perfil.php');
    exit;
}

if ($acao === 'iniciar_quiz') {
    require_once("../dados/perguntas_quiz.php");
    shuffle($perguntas_quiz);
    $_SESSION['quiz'] = [
        'perguntas' => array_slice($perguntas_quiz, 0, 8),
        'atual' => 0,
        'acertos' => 0,
        'moedas' => 0,
        'respostas' => [],
        'habilidade_usada' => false
    ];
    header('Location:../view/quiz.php');
    exit;
}

if ($acao === 'responder_quiz') {
    if (empty($_SESSION['quiz'])) {
        header('Location:../view/quiz.php');
        exit;
    }
    $quiz = &$_SESSION['quiz'];
    $idx = (int)$quiz['atual'];
    $pergunta = $quiz['perguntas'][$idx];
    $resposta = $_POST['resposta'] ?? '';
    $correta = $resposta === $pergunta['correta'];
    if (!$correta && !empty($quiz['segunda_chance_ativa'])) {
        unset($quiz['segunda_chance_ativa']);
        $_SESSION['MensagemQuiz'] = 'Segunda chance ativada. Tente essa pergunta mais uma vez.';
        header('Location:../view/quiz.php');
        exit;
    }
    $ganho = 0;
    if ($correta) {
        $quiz['acertos']++;
        $ganho = $pergunta['moedas'];
        if (!empty($quiz['bonus_moedas_ativo'])) {
            $ganho += 10;
        }
        $quiz['moedas'] += $ganho;
    }
    $quiz['respostas'][] = [
        'enunciado' => $pergunta['enunciado'],
        'correta' => $correta,
        'explicacao' => $pergunta['explicacao'],
        'ganho' => $ganho
    ];
    $quiz['atual']++;
    header('Location:../view/quiz.php');
    exit;
}

if ($acao === 'usar_habilidade') {
    if (empty($_SESSION['quiz'])) {
        header('Location:../view/quiz.php');
        exit;
    }
    $itemId = (int)($_POST['item_id'] ?? 0);
    $equipados = $banco->listarItensEquipados($usuarioId);
    $item = null;
    foreach ($equipados as $equipado) {
        if ((int)$equipado['id'] === $itemId) {
            $item = $equipado;
            break;
        }
    }
    if (!$item) {
        $_SESSION['MensagemQuiz'] = 'Equipe o item antes de usar a habilidade.';
        header('Location:../view/quiz.php');
        exit;
    }
    if (!empty($_SESSION['quiz']['habilidades_usadas'][$itemId])) {
        $_SESSION['MensagemQuiz'] = 'Essa habilidade já foi usada nesta partida.';
        header('Location:../view/quiz.php');
        exit;
    }

    $quiz = &$_SESSION['quiz'];
    $idx = (int)$quiz['atual'];
    $pergunta = $quiz['perguntas'][$idx] ?? null;
    $habilidade = $item['habilidade'];
    $mensagem = 'Habilidade usada.';

    if ($pergunta && stripos($habilidade, 'Dica') !== false) {
        $mensagem = 'Dica: ' . $pergunta['explicacao'];
    } elseif ($pergunta && stripos($habilidade, 'Eliminar') !== false) {
        foreach ($pergunta['alternativas'] as $letra => $texto) {
            if ($letra !== $pergunta['correta']) {
                $quiz['eliminada'] = $letra;
                $mensagem = 'Alternativa eliminada: ' . $letra . '.';
                break;
            }
        }
    } elseif (stripos($habilidade, 'Voltar') !== false) {
        if ($idx <= 0 || empty($quiz['respostas'])) {
            $_SESSION['MensagemQuiz'] = 'Você ainda não respondeu nenhuma pergunta para voltar.';
            header('Location:../view/quiz.php');
            exit;
        }
        $ultimaResposta = array_pop($quiz['respostas']);
        $quiz['atual'] = max(0, $idx - 1);
        if (!empty($ultimaResposta['correta'])) {
            $quiz['acertos'] = max(0, (int)$quiz['acertos'] - 1);
            $quiz['moedas'] = max(0, (int)$quiz['moedas'] - (int)($ultimaResposta['ganho'] ?? 0));
        }
        $mensagem = 'Você voltou uma pergunta. Responda novamente para recuperar o avanço.';
    } elseif (stripos($habilidade, 'Segunda chance') !== false) {
        $quiz['segunda_chance_ativa'] = true;
        $mensagem = 'Segunda chance preparada para a próxima resposta errada.';
    } elseif (stripos($habilidade, 'Tempo') !== false) {
        $quiz['tempo_extra'] = true;
        $mensagem = 'Tempo extra ativado. Jogue com calma.';
    } elseif (stripos($habilidade, 'perfeito') !== false) {
        $quiz['bonus_perfeito_ativo'] = true;
        $mensagem = 'Bônus perfeito ativo: se gabaritar, recebe +25 moedas extras.';
    } elseif (stripos($habilidade, 'moedas') !== false || stripos($habilidade, 'Bônus') !== false) {
        $quiz['bonus_moedas_ativo'] = true;
        $mensagem = 'Bônus ativo: acertos passam a render +10 moedas nesta partida.';
    } elseif ($pergunta && stripos($habilidade, 'Pular') !== false) {
        $quiz['respostas'][] = [
            'enunciado' => $pergunta['enunciado'],
            'correta' => false,
            'explicacao' => 'Pergunta pulada com habilidade.',
            'ganho' => 0
        ];
        $quiz['atual']++;
        $mensagem = 'Pergunta pulada.';
    } elseif (stripos($habilidade, 'pista') !== false) {
        $mensagem = 'Pista: observe o conceito central da pergunta antes de escolher.';
    } elseif (stripos($habilidade, 'ponteiros') !== false) {
        $mensagem = 'Ponteiros destacados: procure quem aponta para o próximo nó.';
    } elseif (stripos($habilidade, 'Anima') !== false) {
        $quiz['animacao_extra'] = true;
        $mensagem = 'Efeito visual ativado para esta partida.';
    }

    $quiz['habilidades_usadas'][$itemId] = true;
    $_SESSION['MensagemQuiz'] = $mensagem;
    header('Location:../view/quiz.php');
    exit;
}

if ($acao === 'finalizar_quiz') {
    if (!empty($_SESSION['quiz'])) {
        $quiz = $_SESSION['quiz'];
        if ($quiz['acertos'] === count($quiz['perguntas'])) {
            $quiz['moedas'] += 50;
            if (!empty($quiz['bonus_perfeito_ativo'])) {
                $quiz['moedas'] += 25;
            }
        }
        $banco->registrarPartida($usuarioId, $quiz['acertos'], count($quiz['perguntas']), $quiz['moedas']);
        $_SESSION['resultado_quiz'] = $quiz;
        unset($_SESSION['quiz']);
    }
    header('Location:../view/resultado_quiz.php');
    exit;
}

if ($acao === 'comprar_item') {
    $_SESSION['MensagemLoja'] = $banco->comprarItem($usuarioId, (int)($_POST['item_id'] ?? 0));
    header('Location:../view/loja.php');
    exit;
}

if ($acao === 'equipar_item') {
    $_SESSION['MensagemInventario'] = $banco->equiparItem($usuarioId, (int)($_POST['item_id'] ?? 0)) ? 'Item equipado.' : 'Item indisponível.';
    header('Location:../view/inventario.php');
    exit;
}

header('Location:../view/home.php');
exit;
?>
