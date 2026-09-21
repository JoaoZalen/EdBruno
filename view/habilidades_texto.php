<?php
function explicarHabilidade(string $habilidade): string
{
    $habilidadeLower = function_exists('mb_strtolower')
        ? mb_strtolower($habilidade, 'UTF-8')
        : strtolower($habilidade);

    if (str_contains($habilidadeLower, 'dica')) {
        return 'Mostra uma dica baseada na explicação da pergunta atual.';
    }
    if (str_contains($habilidadeLower, 'eliminar')) {
        return 'Remove uma alternativa errada da pergunta atual.';
    }
    if (str_contains($habilidadeLower, 'voltar')) {
        return 'Volta para a pergunta anterior. Só funciona depois que você já respondeu pelo menos uma.';
    }
    if (str_contains($habilidadeLower, 'tempo')) {
        return 'Ativa tempo extra para responder a partida com mais calma.';
    }
    if (str_contains($habilidadeLower, 'moedas')) {
        return 'Cada acerto passa a render +10 moedas nesta partida.';
    }
    if (str_contains($habilidadeLower, 'pular')) {
        return 'Pula a pergunta atual sem ganhar moedas por ela.';
    }
    if (str_contains($habilidadeLower, 'pista')) {
        return 'Mostra uma pista curta para orientar a resposta.';
    }
    if (str_contains($habilidadeLower, 'ponteiros')) {
        return 'Ajuda a identificar referências e ligações entre nós.';
    }
    if (str_contains($habilidadeLower, 'perfeito')) {
        return 'Se gabaritar o quiz, adiciona +25 moedas ao bônus perfeito.';
    }
    if (str_contains($habilidadeLower, 'animação')) {
        return 'Ativa um efeito visual extra durante a partida.';
    }

    return 'Habilidade especial para usar durante o quiz.';
}
?>
