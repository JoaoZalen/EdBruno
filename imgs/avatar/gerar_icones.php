<?php
/**
 * gerar_icones.php — Gera os ícones dos itens a partir do desenho do avatar
 *
 * Assim o ícone da loja é exatamente a mesma peça que aparece no personagem.
 * Rodar pelo terminal, dentro desta pasta:
 *     php gerar_icones.php
 */

require_once __DIR__ . '/../../view/avatar.php';

// raridade de cada item (igual ao ed_ensino.sql)
$itens = [
    'oculos_foco'      => 'comum',
    'cabelo_relampago' => 'comum',
    'chapeu_logica'    => 'raro',
    'roupa_tempo'      => 'raro',
    'amuleto_moedas'   => 'epico',
    'mascara_binaria'  => 'raro',
    'capa_recursao'    => 'epico',
    'luvas_ponteiro'   => 'raro',
    'botas_busca'      => 'comum',
    'coroa_tad'        => 'epico',
];

foreach ($itens as $chave => $raridade) {
    file_put_contents(__DIR__ . "/$chave.svg", iconeItemSvg($chave, $raridade));
    echo "ok  $chave.svg\n";
}

file_put_contents(__DIR__ . '/base_avatar.svg', avatarBaseArquivo());
echo "ok  base_avatar.svg\n";
