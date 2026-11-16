<?php
/**
 * avatar_itens.php — Em qual parte do corpo cada item fica
 *
 * No banco, os itens têm uma "categoria" (rosto, cabelo, chapeu, roupa,
 * acessorio). Para o avatar isso não basta: amuleto, luvas e botas são
 * todos "acessorio", mas ficam em lugares diferentes (pescoço, mãos, pés).
 *
 * Por isso cada item tem um SLOT. Só pode haver um item equipado por slot.
 * A chave do item é o nome do arquivo da imagem, sem ".svg".
 */

const AVATAR_SLOTS = [
    'chapeu'  => 'Chapéu',
    'cabelo'  => 'Cabelo',
    'rosto'   => 'Rosto',
    'pescoco' => 'Pescoço',
    'roupa'   => 'Roupa',
    'maos'    => 'Mãos',
    'pes'     => 'Pés',
];

const AVATAR_SLOT_DO_ITEM = [
    'chapeu_logica'    => 'chapeu',
    'coroa_tad'        => 'chapeu',
    'cabelo_relampago' => 'cabelo',
    'oculos_foco'      => 'rosto',
    'mascara_binaria'  => 'rosto',
    'amuleto_moedas'   => 'pescoco',
    'roupa_tempo'      => 'roupa',
    'capa_recursao'    => 'roupa',
    'luvas_ponteiro'   => 'maos',
    'botas_busca'      => 'pes',
];

// "imgs/avatar/coroa_tad.svg" -> "coroa_tad"
function chaveDoItem(array $item): string
{
    return basename((string)($item['imagem'] ?? ''), '.svg');
}

// slot do item; itens novos sem mapeamento usam a própria categoria
function slotDoItem(array $item): string
{
    $chave = chaveDoItem($item);
    return AVATAR_SLOT_DO_ITEM[$chave] ?? (string)($item['categoria'] ?? 'acessorio');
}
?>
