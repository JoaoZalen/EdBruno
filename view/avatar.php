<?php
/**
 * avatar.php — Desenho do avatar em camadas (SVG)
 * Caminho: view/avatar.php
 *
 * O personagem é montado como uma pilha de camadas, da mais ao fundo
 * para a mais à frente (igual a um desenho em papel vegetal):
 *
 *   0 aura      → brilho de fundo (depende da raridade dos itens)
 *   1 costas    → parte de trás da capa
 *   2 sombra    3 pernas    4 pés
 *   5 corpo     → tronco, braços e camiseta padrão
 *   6 roupa     7 pescoço   8 mãos
 *   9 cabeça    10 rosto (olhos e boca)    11 item de rosto
 *   12 cabelo   13 chapéu   14 efeitos (brilhos)
 *
 * Cada item equipado acrescenta partes em uma ou mais camadas.
 * Uso:
 *   echo avatarSvg($itensEquipados);                         // corpo inteiro
 *   echo avatarSvg($itensEquipados, ['recorte' => 'cabeca']); // só o rosto (header)
 *   echo avatarSvg($equipados, ['provador' => $todosItens]);  // permite "provar" itens
 */

require_once __DIR__ . '/../model/avatar_itens.php';

/* =============================================================
   ARTE — cada parte é [camada, svg]
   ============================================================= */
function avatarArte(): array
{
    static $arte = null;
    if ($arte !== null) return $arte;

    $pele      = '#f2c6a0';
    $peleSomb  = '#e5b590';
    $azul      = '#2563eb';
    $azulEsc   = '#1d4ed8';
    $tinta     = '#0b1220';
    $amarelo   = '#facc15';
    $ouro      = '#eab308';
    $ouroEsc   = '#ca8a04';
    $verde     = '#059669';

    // estrela de 4 pontas (brilhos)
    $estrela = function (float $x, float $y, float $s, string $classe = 'av-brilho') use ($ouro) {
        $k = $s * 0.28;
        return "<path class='$classe' d='M$x " . ($y - $s) . " L" . ($x + $k) . " " . ($y - $k) . " L" . ($x + $s) . " $y L" . ($x + $k) . " " . ($y + $k) .
               " L$x " . ($y + $s) . " L" . ($x - $k) . " " . ($y + $k) . " L" . ($x - $s) . " $y L" . ($x - $k) . " " . ($y - $k) . " Z' fill='$ouro'/>";
    };

    $bracos = function (string $cor) {
        return "<path d='M88 170 Q72 190 71 212' stroke='$cor' stroke-width='18' stroke-linecap='round' fill='none'/>" .
               "<path d='M152 170 Q168 190 169 212' stroke='$cor' stroke-width='18' stroke-linecap='round' fill='none'/>";
    };
    $tronco = "M84 170 Q84 156 102 154 H138 Q156 156 156 170 L160 224 Q160 232 152 232 H88 Q80 232 80 224 Z";

    $arte = [

        /* ---------- partes fixas do personagem ---------- */
        'base' => [
            [2, "<ellipse cx='120' cy='284' rx='58' ry='8' fill='$tinta' opacity='.12'/>"],
            [3, "<rect x='97' y='224' width='18' height='44' rx='8' fill='#1e293b'/>" .
                "<rect x='125' y='224' width='18' height='44' rx='8' fill='#1e293b'/>"],
            [5, $bracos($azul) .
                "<path d='$tronco' fill='$azul'/>" .
                "<path d='M108 154 Q120 166 132 154 Z' fill='$azulEsc'/>" .
                "<rect x='106' y='186' width='28' height='18' rx='5' fill='#eff6ff'/>" .
                "<text x='120' y='199' text-anchor='middle' font-family='JetBrains Mono, monospace' font-size='11' font-weight='700' fill='$azul'>{ }</text>"],
            [9, "<rect x='110' y='136' width='20' height='24' rx='6' fill='$peleSomb'/>" .
                "<circle cx='67' cy='102' r='10' fill='#e8b892'/>" .
                "<circle cx='173' cy='102' r='10' fill='#e8b892'/>" .
                "<circle cx='120' cy='98' r='54' fill='$pele'/>"],
            [10, "<circle cx='90' cy='118' r='7' fill='#fca5a5' opacity='.55'/>" .
                 "<circle cx='150' cy='118' r='7' fill='#fca5a5' opacity='.55'/>" .
                 "<g class='av-olhos'>" .
                    "<ellipse cx='102' cy='102' rx='6' ry='8' fill='$tinta'/>" .
                    "<ellipse cx='138' cy='102' rx='6' ry='8' fill='$tinta'/>" .
                    "<circle cx='104' cy='99' r='2' fill='#fff'/>" .
                    "<circle cx='140' cy='99' r='2' fill='#fff'/>" .
                 "</g>" .
                 "<path d='M94 88 Q102 83 110 87' stroke='#7c4a2d' stroke-width='3.5' fill='none' stroke-linecap='round'/>" .
                 "<path d='M130 87 Q138 83 146 88' stroke='#7c4a2d' stroke-width='3.5' fill='none' stroke-linecap='round'/>" .
                 "<path d='M108 124 Q120 135 132 124' stroke='#9a3412' stroke-width='4' fill='none' stroke-linecap='round'/>"],
        ],

        /* ---------- o que aparece quando o slot está vazio ---------- */
        'padrao' => [
            'cabelo' => [
                [12, "<path d='M66 100 Q62 46 120 42 Q178 46 174 100 Q168 74 150 66 Q134 80 106 72 Q84 76 66 100 Z' fill='#5b3a29'/>" .
                     "<path d='M96 52 Q112 46 128 48' stroke='#7c5038' stroke-width='4' fill='none' stroke-linecap='round'/>"],
            ],
            'pes' => [
                [4, "<rect x='91' y='263' width='28' height='13' rx='6.5' fill='#fff' stroke='#cbd5e1' stroke-width='2'/>" .
                    "<rect x='121' y='263' width='28' height='13' rx='6.5' fill='#fff' stroke='#cbd5e1' stroke-width='2'/>" .
                    "<path d='M97 269.5 h14 M127 269.5 h14' stroke='$azul' stroke-width='2.5' stroke-linecap='round'/>"],
            ],
            'maos' => [
                [8, "<circle cx='71' cy='220' r='9' fill='$pele'/><circle cx='169' cy='220' r='9' fill='$pele'/>"],
            ],
        ],

        /* ---------- itens (a chave é o nome do arquivo da imagem) ---------- */
        'itens' => [

            'cabelo_relampago' => [
                [12, "<path d='M64 102 L58 64 L80 72 L80 38 L102 58 L112 22 L126 52 L146 26 L150 60 L174 44 L170 78 L182 102 Q170 74 150 66 Q132 80 106 72 Q84 76 64 102 Z' fill='$amarelo' stroke='$ouro' stroke-width='3' stroke-linejoin='round'/>" .
                     "<path class='av-faisca' d='M131 30 L120 54 L130 54 L121 76 L143 46 L133 46 L141 30 Z' fill='$azul' stroke='$azulEsc' stroke-width='1.5' stroke-linejoin='round'/>"],
            ],

            'oculos_foco' => [
                [11, "<path d='M86 100 L68 96 M154 100 L172 96' stroke='$azulEsc' stroke-width='3.5' stroke-linecap='round'/>" .
                     "<circle cx='102' cy='102' r='15' fill='#dbeafe' fill-opacity='.45' stroke='$azul' stroke-width='4'/>" .
                     "<circle cx='138' cy='102' r='15' fill='#dbeafe' fill-opacity='.45' stroke='$azul' stroke-width='4'/>" .
                     "<path d='M117 101 Q120 96 123 101' stroke='$azul' stroke-width='4' fill='none'/>" .
                     "<path class='av-reflexo' d='M94 95 Q98 91 103 91 M130 95 Q134 91 139 91' stroke='#fff' stroke-width='3' fill='none' stroke-linecap='round'/>"],
            ],

            'mascara_binaria' => [
                [11, "<path d='M72 98 L62 95 M168 98 L178 95' stroke='$tinta' stroke-width='4' stroke-linecap='round'/>" .
                     "<rect x='72' y='88' width='96' height='28' rx='14' fill='$tinta'/>" .
                     "<rect x='72' y='88' width='96' height='28' rx='14' fill='none' stroke='$verde' stroke-width='2'/>" .
                     "<text class='av-binario' x='120' y='107' text-anchor='middle' font-family='JetBrains Mono, monospace' font-size='13' font-weight='700' fill='#34d399' letter-spacing='1'>10110</text>"],
            ],

            'chapeu_logica' => [
                [13, "<path d='M86 60 L86 78 Q120 92 154 78 L154 60 Z' fill='#1e293b'/>" .
                     "<path d='M56 54 L120 30 L184 54 L120 78 Z' fill='$tinta' stroke='#334155' stroke-width='2' stroke-linejoin='round'/>" .
                     "<g class='av-pendulo'>" .
                        "<path d='M120 54 L170 62 L170 90' stroke='$ouro' stroke-width='3' fill='none' stroke-linecap='round' stroke-linejoin='round'/>" .
                        "<rect x='165.5' y='88' width='9' height='13' rx='3' fill='$ouro'/>" .
                     "</g>" .
                     "<circle cx='120' cy='54' r='4.5' fill='$azul'/>"],
            ],

            'coroa_tad' => [
                [13, "<path d='M78 64 L82 26 L101 46 L120 16 L139 46 L158 26 L162 64 Z' fill='$amarelo' stroke='$ouroEsc' stroke-width='3' stroke-linejoin='round'/>" .
                     "<rect x='76' y='58' width='88' height='13' rx='5' fill='$ouro' stroke='$ouroEsc' stroke-width='2'/>" .
                     "<circle cx='120' cy='64.5' r='5' fill='$azul'/>" .
                     "<circle cx='98' cy='64.5' r='4' fill='$verde'/>" .
                     "<circle cx='142' cy='64.5' r='4' fill='#dc2626'/>" .
                     "<circle cx='82' cy='25' r='4' fill='#fef9c3'/><circle cx='120' cy='15' r='4.5' fill='#fef9c3'/><circle cx='158' cy='25' r='4' fill='#fef9c3'/>"],
                [14, $estrela(178, 32, 8) . $estrela(62, 44, 5.5, 'av-brilho av-brilho--2') . $estrela(190, 76, 4.5, 'av-brilho av-brilho--3')],
            ],

            'roupa_tempo' => [
                [6, $bracos('#1e293b') .
                    "<path d='$tronco' fill='#1e293b'/>" .
                    "<path d='M106 154 L120 178 L134 154 Z' fill='$azul'/>" .
                    "<path d='M105 155 L120 178 L111 187 L99 160 Z' fill='#334155'/>" .
                    "<path d='M135 155 L120 178 L129 187 L141 160 Z' fill='#334155'/>" .
                    "<path d='M120 187 V230' stroke='#475569' stroke-width='2'/>" .
                    "<circle cx='120' cy='211' r='13' fill='#fefce8' stroke='$ouro' stroke-width='3'/>" .
                    "<path d='M120 201 v2 M130 211 h-2 M120 221 v-2 M110 211 h2' stroke='#a16207' stroke-width='2' stroke-linecap='round'/>" .
                    "<path d='M120 211 V204' stroke='$tinta' stroke-width='2.5' stroke-linecap='round'/>" .
                    "<path class='av-ponteiro-relogio' d='M120 211 L126 215' stroke='$azul' stroke-width='2.5' stroke-linecap='round'/>" .
                    "<circle cx='120' cy='211' r='2' fill='$tinta'/>"],
            ],

            'capa_recursao' => [
                [1, "<g class='av-capa'>" .
                        "<path d='M86 158 Q58 214 50 276 Q120 290 190 276 Q182 214 154 158 Z' fill='$azulEsc'/>" .
                        "<path d='M86 158 Q62 214 56 272 Q64 275 70 270 Q76 212 96 164 Z' fill='$verde'/>" .
                        "<path d='M154 158 Q178 214 184 272 Q176 275 170 270 Q164 212 144 164 Z' fill='$verde'/>" .
                        "<g fill='none' stroke='#93c5fd' stroke-width='2' opacity='.75'>" .
                            "<circle cx='79' cy='246' r='11'/><circle cx='79' cy='246' r='6.5'/><circle cx='79' cy='246' r='2'/>" .
                            "<circle cx='161' cy='246' r='11'/><circle cx='161' cy='246' r='6.5'/><circle cx='161' cy='246' r='2'/>" .
                        "</g>" .
                    "</g>"],
                [6, "<path d='M90 156 L104 174 L120 163 L136 174 L150 156 Q120 148 90 156 Z' fill='#1e40af'/>" .
                    "<circle cx='120' cy='167' r='7' fill='$ouro' stroke='$ouroEsc' stroke-width='2'/>" .
                    "<path d='M120 167 m-3.2 0 a3.2 3.2 0 1 1 3.2 3.2' stroke='#a16207' stroke-width='1.6' fill='none' stroke-linecap='round'/>"],
            ],

            'amuleto_moedas' => [
                [7, "<path d='M100 156 Q120 184 140 156' stroke='$ouro' stroke-width='3' fill='none'/>" .
                    "<g class='av-moeda'>" .
                        "<circle cx='120' cy='184' r='12.5' fill='$amarelo' stroke='$ouroEsc' stroke-width='3'/>" .
                        "<text x='120' y='189.5' text-anchor='middle' font-family='Plus Jakarta Sans, sans-serif' font-size='14' font-weight='800' fill='#a16207'>$</text>" .
                    "</g>"],
            ],

            'luvas_ponteiro' => [
                [8, "<rect x='61' y='205' width='20' height='9' rx='3' fill='#93c5fd'/>" .
                    "<rect x='159' y='205' width='20' height='9' rx='3' fill='#93c5fd'/>" .
                    "<circle cx='71' cy='221' r='11' fill='$azul'/>" .
                    "<circle cx='169' cy='221' r='11' fill='$azul'/>" .
                    "<path class='av-pulso' d='M66 221 h9 m-3.5 -3.5 l3.5 3.5 l-3.5 3.5 M164 221 h9 m-3.5 -3.5 l3.5 3.5 l-3.5 3.5' stroke='#fff' stroke-width='2.2' fill='none' stroke-linecap='round' stroke-linejoin='round'/>"],
            ],

            'botas_busca' => [
                [4, "<rect x='94' y='242' width='24' height='30' rx='6' fill='$verde'/>" .
                    "<rect x='122' y='242' width='24' height='30' rx='6' fill='$verde'/>" .
                    "<rect x='92' y='238' width='28' height='9' rx='4' fill='#047857'/>" .
                    "<rect x='120' y='238' width='28' height='9' rx='4' fill='#047857'/>" .
                    "<rect x='90' y='268' width='32' height='9' rx='4.5' fill='$tinta'/>" .
                    "<rect x='118' y='268' width='32' height='9' rx='4.5' fill='$tinta'/>" .
                    "<g class='av-lupa' fill='none' stroke='#fff' stroke-width='2' stroke-linecap='round'>" .
                        "<circle cx='105' cy='256' r='4.5'/><path d='M108.5 259.5 l3.5 3.5'/>" .
                        "<circle cx='133' cy='256' r='4.5'/><path d='M136.5 259.5 l3.5 3.5'/>" .
                    "</g>"],
            ],
        ],
    ];

    return $arte;
}

/* Região de cada item para gerar o ícone (x, y, largura, altura) */
const AVATAR_RECORTE_ICONE = [
    'cabelo_relampago' => [52, 16, 136, 92],
    'oculos_foco'      => [62, 82, 116, 38],
    'mascara_binaria'  => [60, 84, 120, 36],
    'chapeu_logica'    => [52, 26, 136, 80],
    'coroa_tad'        => [56, 6, 140, 76],
    'roupa_tempo'      => [60, 150, 120, 86],
    'capa_recursao'    => [46, 150, 148, 138],
    'amuleto_moedas'   => [96, 152, 48, 48],
    'luvas_ponteiro'   => [57, 202, 28, 32],
    'botas_busca'      => [86, 234, 68, 46],
];

const AVATAR_RARIDADE_NIVEL = ['comum' => 0, 'raro' => 1, 'epico' => 2];

/* =============================================================
   AVATAR COMPLETO
   $equipados: itens equipados (linhas da tabela itens)
   $op:
     'recorte'  => 'inteiro' | 'cabeca'
     'classe'   => classe CSS extra
     'provador' => itens que podem ser "provados" (ficam ocultos)
     'recem'    => chave do item recém-equipado (anima a chegada)
     'titulo'   => texto alternativo
   ============================================================= */
function avatarSvg(array $equipados, array $op = []): string
{
    static $instancia = 0;
    $instancia++;

    $arte    = avatarArte();
    $recorte = $op['recorte'] ?? 'inteiro';
    $classe  = trim('avatar ' . ($op['classe'] ?? '') . ' avatar--' . $recorte);
    $titulo  = htmlspecialchars($op['titulo'] ?? 'Avatar do personagem');
    $recem   = $op['recem'] ?? null;

    // slot => chave do item equipado
    $porSlot  = [];
    $raridade = 0;
    foreach ($equipados as $item) {
        $chave = chaveDoItem($item);
        if (!isset($arte['itens'][$chave])) continue;
        $porSlot[slotDoItem($item)] = $chave;
        $raridade = max($raridade, AVATAR_RARIDADE_NIVEL[$item['raridade'] ?? 'comum'] ?? 0);
    }

    // itens que entram no desenho: equipados + os que podem ser provados
    $candidatos = [];
    foreach (array_merge($equipados, $op['provador'] ?? []) as $item) {
        $chave = chaveDoItem($item);
        if (isset($arte['itens'][$chave])) $candidatos[$chave] = slotDoItem($item);
    }

    // separa as partes por camada
    $camadas = array_fill(0, 15, '');

    foreach ($arte['base'] as [$nivel, $svg]) {
        $camadas[$nivel] .= $svg;
    }

    foreach ($arte['padrao'] as $slot => $partes) {
        $visivel = !isset($porSlot[$slot]);
        foreach ($partes as [$nivel, $svg]) {
            $camadas[$nivel] .= avatarGrupo($slot, 'padrao', $visivel, false, $svg);
        }
    }

    foreach ($candidatos as $chave => $slot) {
        $visivel = (($porSlot[$slot] ?? null) === $chave);
        foreach ($arte['itens'][$chave] as [$nivel, $svg]) {
            $camadas[$nivel] .= avatarGrupo($slot, $chave, $visivel, $visivel && $recem === $chave, $svg);
        }
    }

    // aura de fundo conforme a melhor raridade equipada
    $id = 'av' . $instancia;
    $defs = '';
    if ($raridade > 0) {
        $cor = $raridade === 2 ? '#facc15' : '#60a5fa';
        $defs = "<defs><radialGradient id='$id-aura'><stop offset='0' stop-color='$cor' stop-opacity='.55'/>" .
                "<stop offset='.6' stop-color='$cor' stop-opacity='.16'/><stop offset='1' stop-color='$cor' stop-opacity='0'/></radialGradient></defs>";
        $aura = "<circle class='av-aura' cx='120' cy='150' r='122' fill='url(#$id-aura)'/>";
        if ($raridade === 2) {
            $raios = '';
            for ($i = 0; $i < 12; $i++) {
                $raios .= "<path d='M120 150 L114 30 L126 30 Z' transform='rotate(" . ($i * 30) . " 120 150)'/>";
            }
            $aura .= "<g class='av-raios' fill='#facc15' opacity='.16'>$raios</g>";
        }
        $camadas[0] = $aura . $camadas[0];
    }

    $viewBox = $recorte === 'cabeca' ? '50 8 140 140' : '0 0 240 300';
    $classe .= ' avatar--raridade-' . $raridade;

    return "<svg class='$classe' viewBox='$viewBox' xmlns='http://www.w3.org/2000/svg' role='img' aria-label='$titulo' data-avatar='1'>" .
           $defs . "<g class='av-personagem'>" . implode('', $camadas) . "</g></svg>";
}

function avatarGrupo(string $slot, string $chave, bool $visivel, bool $recem, string $svg): string
{
    $classe = 'av-camada' . ($visivel ? '' : ' av-oculta') . ($recem ? ' av-recem' : '');
    return "<g class='$classe' data-slot='$slot' data-item='$chave' data-visivel='" . ($visivel ? 1 : 0) . "'>$svg</g>";
}

/* =============================================================
   ÍCONE DE UM ITEM (usado para gerar os arquivos imgs/avatar/*.svg)
   ============================================================= */
function iconeItemSvg(string $chave, string $raridade = 'comum'): string
{
    $arte = avatarArte();
    if (!isset($arte['itens'][$chave], AVATAR_RECORTE_ICONE[$chave])) return '';

    [$x, $y, $w, $h] = AVATAR_RECORTE_ICONE[$chave];
    $lado = max($w, $h) * 1.18;
    $vx = $x + $w / 2 - $lado / 2;
    $vy = $y + $h / 2 - $lado / 2;

    $cores = [
        'comum' => ['#f8fafc', '#e2e8f0', '#cbd5e1'],
        'raro'  => ['#eff6ff', '#bfdbfe', '#93c5fd'],
        'epico' => ['#fefce8', '#fde68a', '#eab308'],
    ][$raridade] ?? ['#f8fafc', '#e2e8f0', '#cbd5e1'];

    $partes = '';
    foreach ($arte['itens'][$chave] as [, $svg]) $partes .= $svg;

    $r = $lado * 0.16;
    $borda = $lado * 0.025;   // borda proporcional ao tamanho do recorte
    return "<svg xmlns='http://www.w3.org/2000/svg' viewBox='$vx $vy $lado $lado'>" .
           "<defs><radialGradient id='f' cx='.5' cy='.35' r='.75'><stop offset='0' stop-color='{$cores[0]}'/><stop offset='1' stop-color='{$cores[1]}'/></radialGradient></defs>" .
           "<rect x='$vx' y='$vy' width='$lado' height='$lado' rx='$r' fill='url(#f)'/>" .
           "<rect x='" . ($vx + $borda / 2) . "' y='" . ($vy + $borda / 2) . "' width='" . ($lado - $borda) . "' height='" . ($lado - $borda) . "' rx='$r' fill='none' stroke='{$cores[2]}' stroke-width='$borda'/>" .
           $partes . "</svg>";
}

/* =============================================================
   SLOTS DO CORPO (lista com o que está equipado em cada parte)
   $voltar: página para onde o formulário de remover volta
            (null = sem botão de remover)
   ============================================================= */
function avatarSlotsHtml(array $equipados, ?string $voltar = null): string
{
    $porSlot = [];
    foreach ($equipados as $item) $porSlot[slotDoItem($item)] = $item;

    $html = "<ul class='av-slots'>";
    foreach (AVATAR_SLOTS as $slot => $nome) {
        $item = $porSlot[$slot] ?? null;
        if ($item) {
            $rar   = htmlspecialchars($item['raridade']);
            $img   = htmlspecialchars('../' . $item['imagem']);
            $nomeI = htmlspecialchars($item['nome']);
            $html .= "<li class='av-slot cheio raridade-$rar' title='$nome: $nomeI'>" .
                     "<img src='$img' alt=''><span class='av-slot-texto'><small>$nome</small>$nomeI</span>";
            if ($voltar) {
                $html .= "<form method='post' action='../processamento/processamento.php'>" .
                         "<input type='hidden' name='acao' value='desequipar_item'>" .
                         "<input type='hidden' name='item_id' value='" . (int)$item['id'] . "'>" .
                         "<input type='hidden' name='voltar' value='" . htmlspecialchars($voltar) . "'>" .
                         "<button type='submit' class='av-slot-remover' aria-label='Remover $nomeI' title='Remover'>×</button></form>";
            }
            $html .= "</li>";
        } else {
            $html .= "<li class='av-slot vazio'><span class='av-slot-icone'></span><span class='av-slot-texto'><small>$nome</small>vazio</span></li>";
        }
    }
    return $html . "</ul>";
}

/* Avatar padrão (sem itens) com fundo, para imagens estáticas */
function avatarBaseArquivo(): string
{
    $svg = avatarSvg([]);
    return str_replace("<g class='av-personagem'>",
        "<rect x='0' y='0' width='240' height='300' rx='34' fill='#eff6ff'/><g class='av-personagem'>", $svg);
}
?>
