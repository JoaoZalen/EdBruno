/* =============================================================
   simulador.js — Simulador de Estruturas de Dados
   Sistema: ED Ensino
   Caminho: js/simulador.js

   Visão geral:
   1. Estado        → "nos" (listas, pilha, filas) ou "raiz" (árvore)
   2. Layout        → calcula onde cada nó deveria ficar (x, y)
   3. desenhar()    → cria/move os nós na tela (com transição CSS)
   4. Linhas        → um laço (requestAnimationFrame) redesenha as
                      ligações a cada quadro, lendo a posição REAL dos
                      nós. Por isso elas acompanham animações e arrastos.
   5. Operações     → funções async que executam o algoritmo passo a
                      passo, destacando a linha do código em C#.
   6. Interação     → arrastar nós, zoom, mover o fundo, botão direito,
                      modo passo a passo e tela cheia.
   ============================================================= */

(function () {
    'use strict';

    var app = document.getElementById('simulador');
    if (!app) return;

    var TIPO = app.dataset.estrutura;

    function pegar(id) { return document.getElementById(id); }

    /* ---------- Elementos da página ---------- */
    var palco      = pegar('sim-palco');
    var mundo      = pegar('sim-mundo');
    var camadaNos  = pegar('sim-nos');
    var svg        = pegar('sim-links');
    var vazio      = pegar('sim-vazio');
    var info       = pegar('sim-info');
    var narracao   = pegar('sim-narracao');
    var narrTexto  = pegar('sim-narracao-texto');
    var btnProximo = pegar('sim-proximo');
    var codigoEl   = pegar('sim-codigo');
    var codigoTit  = pegar('sim-codigo-titulo');
    var historico  = pegar('sim-log');
    var caixaOps   = pegar('sim-operacoes');
    var campoValor = pegar('sim-valor');
    var campoPos   = pegar('sim-posicao');
    var campoPrio  = pegar('sim-prioridade');
    var velocidade = pegar('sim-velocidade');
    var menu       = pegar('sim-menu');

    /* =============================================================
       1. CONFIGURAÇÃO E ESTADO
       forma: 'linha' (horizontal) | 'coluna' (pilha) | 'arvore'
       ============================================================= */
    var CONFIG = {
        lista_simples: {
            forma: 'linha', indices: true, max: 12,
            ponteiros: [['cabeça', 'primeiro']],
            ops: ['inserirInicio', 'inserirFim', 'inserirPosicao', 'removerInicio',
                  'removerFim', 'removerValor', 'removerPosicao', 'buscar']
        },
        lista_dupla: {
            forma: 'linha', duplo: true, indices: true, max: 12,
            ponteiros: [['cabeça', 'primeiro'], ['cauda', 'ultimo']],
            ops: ['inserirInicio', 'inserirFim', 'inserirPosicao', 'removerInicio',
                  'removerFim', 'removerValor', 'removerPosicao', 'buscar', 'percorrerReverso']
        },
        pilha: {
            forma: 'coluna', max: 10,
            ponteiros: [['topo', 'primeiro']],
            ops: ['empilhar', 'desempilhar', 'espiar']
        },
        fila_fifo: {
            forma: 'linha', max: 12,
            ponteiros: [['início', 'primeiro'], ['fim', 'ultimo']],
            ops: ['enfileirar', 'desenfileirar', 'frente']
        },
        fila_prioridade: {
            forma: 'linha', prioridade: true, max: 12,
            ponteiros: [['início', 'primeiro']],
            ops: ['enfileirarPrio', 'desenfileirar', 'frente']
        },
        arvore_binaria: {
            forma: 'arvore', max: 25,
            ops: ['arvInserir', 'arvRemover', 'arvBuscar', 'arvEmOrdem', 'arvPreOrdem',
                  'arvPosOrdem', 'arvNivel', 'arvMinimo', 'arvMaximo']
        }
    };

    var CONF = CONFIG[TIPO];
    if (!CONF) return;
    var DUPLA  = !!CONF.duplo;
    var ARVORE = CONF.forma === 'arvore';

    var nos      = [];   // estruturas lineares: [{ id, valor, prio }] na ordem
    var raiz     = null; // árvore: { id, valor, esq, dir }
    var contador = 0;    // gera ids únicos
    var ocupado  = false;

    var unidades  = {};  // id -> { el, caixa, valorEl, no, saindo }
    var manual    = {};  // id -> {x, y}  posições definidas arrastando
    var posicoes  = {};  // id -> {x, y}  onde cada nó está (alvo atual)
    var linksAtuais = [];
    var brilhos   = {};  // chave da linha -> instante em que o brilho termina
    var ultimoAtual = null;

    /* =============================================================
       2. CÓDIGOS EM C# (uma linha por item)
       ============================================================= */
    var COD = {
        inserirInicio: DUPLA ? [
            'No novo = new No(valor);',
            'novo.Proximo = cabeca;',
            'if (cabeca != null) cabeca.Anterior = novo;',
            'cabeca = novo;  if (cauda == null) cauda = novo;'
        ] : [
            'No novo = new No(valor);',
            'novo.Proximo = cabeca;',
            'cabeca = novo;'
        ],
        inserirFim: DUPLA ? [
            'No novo = new No(valor);',
            'if (cabeca == null) { cabeca = cauda = novo; return; }',
            'novo.Anterior = cauda;',
            'cauda.Proximo = novo;',
            'cauda = novo;'
        ] : [
            'No novo = new No(valor);',
            'if (cabeca == null) { cabeca = novo; return; }',
            'No atual = cabeca;',
            'while (atual.Proximo != null)',
            '    atual = atual.Proximo;',
            'atual.Proximo = novo;'
        ],
        inserirPosicao: DUPLA ? [
            'if (pos == 0) { InserirInicio(valor); return; }',
            'if (pos == tamanho) { InserirFim(valor); return; }',
            'No atual = cabeca;',
            'for (int i = 0; i < pos; i++)',
            '    atual = atual.Proximo;',
            'No novo = new No(valor);',
            'novo.Proximo = atual;  novo.Anterior = atual.Anterior;',
            'atual.Anterior.Proximo = novo;',
            'atual.Anterior = novo;'
        ] : [
            'if (pos == 0) { InserirInicio(valor); return; }',
            'No atual = cabeca;',
            'for (int i = 0; i < pos - 1; i++)',
            '    atual = atual.Proximo;',
            'No novo = new No(valor);',
            'novo.Proximo = atual.Proximo;',
            'atual.Proximo = novo;'
        ],
        removerInicio: DUPLA ? [
            'if (cabeca == null) return;',
            'cabeca = cabeca.Proximo;',
            'if (cabeca != null) cabeca.Anterior = null;',
            'else cauda = null;'
        ] : [
            'if (cabeca == null) return;',
            'cabeca = cabeca.Proximo;'
        ],
        removerFim: DUPLA ? [
            'if (cauda == null) return;',
            'cauda = cauda.Anterior;',
            'if (cauda != null) cauda.Proximo = null;',
            'else cabeca = null;'
        ] : [
            'if (cabeca == null) return;',
            'if (cabeca.Proximo == null) { cabeca = null; return; }',
            'No atual = cabeca;',
            'while (atual.Proximo.Proximo != null)',
            '    atual = atual.Proximo;',
            'atual.Proximo = null;'
        ],
        removerValor: DUPLA ? [
            'No atual = cabeca;',
            'while (atual != null && atual.Dado != valor)',
            '    atual = atual.Proximo;',
            'if (atual == null) return;',
            'if (atual.Anterior != null) atual.Anterior.Proximo = atual.Proximo;',
            'else cabeca = atual.Proximo;',
            'if (atual.Proximo != null) atual.Proximo.Anterior = atual.Anterior;',
            'else cauda = atual.Anterior;'
        ] : [
            'if (cabeca == null) return;',
            'if (cabeca.Dado == valor) { cabeca = cabeca.Proximo; return; }',
            'No atual = cabeca;',
            'while (atual.Proximo != null && atual.Proximo.Dado != valor)',
            '    atual = atual.Proximo;',
            'if (atual.Proximo != null)',
            '    atual.Proximo = atual.Proximo.Proximo;'
        ],
        removerPosicao: DUPLA ? [
            'No atual = cabeca;',
            'for (int i = 0; i < pos; i++)',
            '    atual = atual.Proximo;',
            'if (atual.Anterior != null) atual.Anterior.Proximo = atual.Proximo;',
            'else cabeca = atual.Proximo;',
            'if (atual.Proximo != null) atual.Proximo.Anterior = atual.Anterior;',
            'else cauda = atual.Anterior;'
        ] : [
            'if (pos == 0) { cabeca = cabeca.Proximo; return; }',
            'No atual = cabeca;',
            'for (int i = 0; i < pos - 1; i++)',
            '    atual = atual.Proximo;',
            'No alvo = atual.Proximo;',
            'atual.Proximo = alvo.Proximo;'
        ],
        buscar: [
            'No atual = cabeca;',
            'while (atual != null) {',
            '    if (atual.Dado == valor) return atual;',
            '    atual = atual.Proximo;',
            '}',
            'return null;'
        ],
        percorrerReverso: [
            'No atual = cauda;',
            'while (atual != null) {',
            '    Console.Write(atual.Dado + " ");',
            '    atual = atual.Anterior;',
            '}'
        ],
        empilhar: [
            'No novo = new No(valor);',
            'novo.Proximo = topo;',
            'topo = novo;',
            'tamanho++;'
        ],
        desempilhar: [
            'if (topo == null) throw new Exception("Pilha vazia");',
            'int valor = topo.Dado;',
            'topo = topo.Proximo;',
            'tamanho--;',
            'return valor;'
        ],
        espiar: [
            'if (topo == null) throw new Exception("Pilha vazia");',
            'return topo.Dado;'
        ],
        enfileirar: [
            'No novo = new No(valor);',
            'if (fim == null) { inicio = fim = novo; return; }',
            'fim.Proximo = novo;',
            'fim = novo;'
        ],
        desenfileirar: [
            'if (inicio == null) throw new Exception("Fila vazia");',
            'int valor = inicio.Dado;',
            'inicio = inicio.Proximo;',
            TIPO === 'fila_prioridade' ? '// a fila de prioridade não guarda "fim"' : 'if (inicio == null) fim = null;',
            'return valor;'
        ],
        frente: [
            'if (inicio == null) throw new Exception("Fila vazia");',
            'return inicio.Dado;'
        ],
        enfileirarPrio: [
            'No novo = new No(valor, prioridade);',
            'if (inicio == null || prioridade > inicio.Prioridade) {',
            '    novo.Proximo = inicio;  inicio = novo;  return;',
            '}',
            'No atual = inicio;',
            'while (atual.Proximo != null && atual.Proximo.Prioridade >= prioridade)',
            '    atual = atual.Proximo;',
            'novo.Proximo = atual.Proximo;',
            'atual.Proximo = novo;'
        ],

        /* ----- Árvore Binária de Busca ----- */
        arvInserir: [
            'No novo = new No(valor);',
            'if (raiz == null) { raiz = novo; return; }',
            'No atual = raiz;',
            'while (true) {',
            '    if (valor < atual.Valor) {',
            '        if (atual.Esquerda == null) { atual.Esquerda = novo; return; }',
            '        atual = atual.Esquerda;',
            '    } else if (valor > atual.Valor) {',
            '        if (atual.Direita == null) { atual.Direita = novo; return; }',
            '        atual = atual.Direita;',
            '    } else return;  // valor repetido não entra',
            '}'
        ],
        arvBuscar: [
            'No atual = raiz;',
            'while (atual != null) {',
            '    if (valor == atual.Valor) return atual;',
            '    if (valor < atual.Valor) atual = atual.Esquerda;',
            '    else atual = atual.Direita;',
            '}',
            'return null;'
        ],
        arvRemover: [
            'No pai = null, atual = raiz;',
            'while (atual != null && atual.Valor != valor) {',
            '    pai = atual;',
            '    atual = valor < atual.Valor ? atual.Esquerda : atual.Direita;',
            '}',
            'if (atual == null) return;  // não encontrou',
            'if (atual.Esquerda != null && atual.Direita != null) {  // 2 filhos',
            '    No paiSuc = atual, suc = atual.Direita;',
            '    while (suc.Esquerda != null) { paiSuc = suc; suc = suc.Esquerda; }',
            '    atual.Valor = suc.Valor;  // copia o sucessor',
            '    pai = paiSuc;  atual = suc;  // agora remove o sucessor',
            '}',
            'No filho = atual.Esquerda ?? atual.Direita;  // 0 ou 1 filho',
            'if (pai == null) raiz = filho;',
            'else if (pai.Esquerda == atual) pai.Esquerda = filho;',
            'else pai.Direita = filho;'
        ],
        arvEmOrdem: [
            'void EmOrdem(No no) {',
            '    if (no == null) return;',
            '    EmOrdem(no.Esquerda);',
            '    Console.Write(no.Valor + " ");',
            '    EmOrdem(no.Direita);',
            '}'
        ],
        arvPreOrdem: [
            'void PreOrdem(No no) {',
            '    if (no == null) return;',
            '    Console.Write(no.Valor + " ");',
            '    PreOrdem(no.Esquerda);',
            '    PreOrdem(no.Direita);',
            '}'
        ],
        arvPosOrdem: [
            'void PosOrdem(No no) {',
            '    if (no == null) return;',
            '    PosOrdem(no.Esquerda);',
            '    PosOrdem(no.Direita);',
            '    Console.Write(no.Valor + " ");',
            '}'
        ],
        arvNivel: [
            'Queue<No> fila = new Queue<No>();',
            'fila.Enqueue(raiz);',
            'while (fila.Count > 0) {',
            '    No no = fila.Dequeue();',
            '    Console.Write(no.Valor + " ");',
            '    if (no.Esquerda != null) fila.Enqueue(no.Esquerda);',
            '    if (no.Direita != null) fila.Enqueue(no.Direita);',
            '}'
        ],
        arvMinimo: [
            'No atual = raiz;',
            'while (atual.Esquerda != null)',
            '    atual = atual.Esquerda;',
            'return atual.Valor;'
        ],
        arvMaximo: [
            'No atual = raiz;',
            'while (atual.Direita != null)',
            '    atual = atual.Direita;',
            'return atual.Valor;'
        ]
    };

    /* =============================================================
       3. AUXILIARES
       ============================================================= */
    var TEMPOS = [0, 1500, 1050, 720, 460, 260]; // índice = velocidade 1..5

    /* ---- Modo passo a passo: espera() aguarda o botão "Próximo" ---- */
    var modoPasso = false;
    var liberarPasso = null;

    function espera(ms) {
        if (ms === undefined && modoPasso) {
            return new Promise(function (ok) {
                liberarPasso = ok;
                btnProximo.hidden = false;
            });
        }
        var t = (ms === undefined) ? TEMPOS[+velocidade.value] : ms;
        return new Promise(function (ok) { setTimeout(ok, t); });
    }

    function avancarPasso() {
        if (!liberarPasso) return;
        var f = liberarPasso;
        liberarPasso = null;
        btnProximo.hidden = true;
        f();
    }

    function criarNo(valor, prio) {
        contador++;
        return { id: contador, valor: valor, prio: prio, esq: null, dir: null };
    }

    function idDe(no) { return no ? no.id : null; }

    function mesclar(a, b) {
        var r = {}, k;
        for (k in a) r[k] = a[k];
        for (k in b) r[k] = b[k];
        return r;
    }

    function Falha(msg) { this.message = msg; }
    function falhar(msg) { throw new Falha(msg); }

    function sortear() { return Math.floor(Math.random() * 99) + 1; }

    // Para onde cada ponteiro aponta AGORA (antes de uma mudança)
    function fotoPonteiros() {
        var mapa = {};
        if (ARVORE) {
            mapa['raiz'] = idDe(raiz);
            return mapa;
        }
        CONF.ponteiros.forEach(function (p) {
            var no = (p[1] === 'primeiro') ? nos[0] : nos[nos.length - 1];
            mapa[p[0]] = idDe(no);
        });
        return mapa;
    }

    function narrar(texto, tipo) {
        narrTexto.textContent = texto;
        narracao.className = 'sim-narracao' + (tipo ? ' sim-narracao--' + tipo : '');
    }

    function registrar(texto, erro) {
        var li = document.createElement('li');
        li.textContent = texto;
        if (erro) li.className = 'erro';
        historico.insertBefore(li, historico.firstChild);
        while (historico.children.length > 10) historico.removeChild(historico.lastChild);
    }

    /* ---- Árvore: utilidades ---- */
    function emOrdemLista(n, prof, lista) {
        if (!n) return lista;
        emOrdemLista(n.esq, prof + 1, lista);
        lista.push({ no: n, prof: prof });
        emOrdemLista(n.dir, prof + 1, lista);
        return lista;
    }

    function alturaArvore(n) {
        return n ? 1 + Math.max(alturaArvore(n.esq), alturaArvore(n.dir)) : 0;
    }

    function inserirSemAnimar(v) {
        var no = criarNo(v);
        if (!raiz) { raiz = no; return true; }
        var a = raiz;
        while (true) {
            if (v === a.valor) return false;
            if (v < a.valor) { if (!a.esq) { a.esq = no; return true; } a = a.esq; }
            else { if (!a.dir) { a.dir = no; return true; } a = a.dir; }
        }
    }

    function acharNoArvore(id) {
        var achado = null;
        (function rec(n) {
            if (!n || achado) return;
            if (n.id === id) { achado = n; return; }
            rec(n.esq); rec(n.dir);
        })(raiz);
        return achado;
    }

    function totalNos() {
        return ARVORE ? emOrdemLista(raiz, 0, []).length : nos.length;
    }

    /* ---------- Painel de código ---------- */
    var PALAVRAS = /\b(new|if|else|return|while|for|int|null|throw|true|void|No|Exception|Console|Queue)\b/g;

    function escapar(t) {
        return t.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function mostrarCodigo(opId) {
        var op = OPS[opId];
        codigoTit.innerHTML = escapar(op.metodo) + ' <span class="sim-big-o">' + op.custo + '</span>';
        codigoEl.innerHTML = COD[opId].map(function (linha, i) {
            var partes = linha.split('//');
            var html = escapar(partes[0])
                .replace(/("[^"]*")/g, '<span class="s">$1</span>')
                .replace(PALAVRAS, '<span class="k">$1</span>');
            if (partes.length > 1) html += '<span class="c">//' + escapar(partes.slice(1).join('//')) + '</span>';
            return '<span class="sim-linha" data-n="' + (i + 1) + '">' + html + '</span>';
        }).join('');
    }

    function destacarLinha(i) {
        var linhas = codigoEl.querySelectorAll('.sim-linha');
        for (var k = 0; k < linhas.length; k++) {
            linhas[k].classList.toggle('ativa', k === i);
        }
        // rola só a caixa de código (não o painel) até a linha ativa
        var ativa = linhas[i];
        if (ativa) {
            var topo = ativa.offsetTop, fim = topo + ativa.offsetHeight;
            if (topo < codigoEl.scrollTop + 8) codigoEl.scrollTop = topo - 8;
            else if (fim > codigoEl.scrollTop + codigoEl.clientHeight - 8) codigoEl.scrollTop = fim - codigoEl.clientHeight + 8;
        }
    }

    /* =============================================================
       4. LAYOUT — onde cada nó "deveria" ficar (coordenadas do mundo)
       ============================================================= */
    var ESP_X = 170;   // distância entre nós na horizontal
    var ESP_Y = 100;   // distância entre nós da pilha
    var ARV_X = 92;    // árvore: distância horizontal entre colunas
    var ARV_Y = 112;   // árvore: distância entre níveis

    function layoutEstrutura() {
        if (ARVORE) {
            // x = posição na ordem (em ordem), y = profundidade.
            // A raiz fica sempre em x = 0, assim ela não "pula" ao inserir.
            var lista = emOrdemLista(raiz, 0, []);
            var iRaiz = 0;
            lista.forEach(function (it, i) { if (it.no === raiz) iRaiz = i; });
            return lista.map(function (it, i) {
                return { no: it.no, x: (i - iRaiz) * ARV_X, y: it.prof * ARV_Y };
            });
        }
        return nos.map(function (n, i) {
            return CONF.forma === 'coluna'
                ? { no: n, x: 0, y: i * ESP_Y }
                : { no: n, x: i * ESP_X, y: 0 };
        });
    }

    // deslocamento de um nó "solto" (ainda não ligado) ou "descolado" (saindo)
    function deslocar(p, sentido) {
        if (CONF.forma === 'coluna') return { x: p.x + sentido * 175, y: p.y };
        return { x: p.x, y: p.y + sentido * 92 };
    }

    /* =============================================================
       5. DESENHO
       op = {
         atual, novo, alvo, achado: id          → destaques
         visitados: [ids]                       → já visitados (percursos)
         ponteiros: { nome: id|null }           → sobrescreve etiquetas
         tags: { id: ['pai', ...] }             → etiquetas extras
         flutuando: id                          → nó ainda solto (acima)
         descolado: id                          → nó saindo (abaixo)
         fora: [ids]                            → fora da corrente de ligações
         ocultar: [{de, para, tipo}]            → esconde ligações
         extras:  [{de, para, tipo}]            → ligações sendo criadas
         soltos:  [{no, x, y}]                  → nós fora da estrutura
       }
       ============================================================= */
    function criarUnidade(no) {
        var el = document.createElement('div');
        el.className = 'sim-ancora';
        el.dataset.id = no.id;

        var esq = '', dir = '<span class="sim-ptr" data-ptr="prox"></span>';
        if (DUPLA) esq = '<span class="sim-ptr" data-ptr="ant"></span>';
        if (ARVORE) {
            esq = '<span class="sim-ptr" data-ptr="esq"></span>';
            dir = '<span class="sim-ptr" data-ptr="dir"></span>';
        }
        var prio = CONF.prioridade
            ? '<span class="sim-prio" data-p="' + no.prio + '">P' + no.prio + '</span>' : '';

        el.innerHTML =
            '<div class="sim-no-caixa">' +
                '<div class="sim-tags"></div>' +
                '<div class="sim-no">' + esq +
                    '<span class="sim-valor"></span>' + prio + dir +
                '</div>' +
                (CONF.indices ? '<span class="sim-indice"></span>' : '') +
            '</div>';

        var u = {
            el: el,
            caixa: el.querySelector('.sim-no-caixa'),
            valorEl: el.querySelector('.sim-valor'),
            tagsEl: el.querySelector('.sim-tags'),
            no: no,
            valorMostrado: null,
            saindo: false
        };
        ligarArraste(u);
        return u;
    }

    function posicionar(u, p, nova) {
        var t = 'translate(' + p.x + 'px,' + p.y + 'px)';
        if (nova) {
            u.el.classList.add('sem-transicao');
            u.el.style.transform = 'translate(' + p.x + 'px,' + (p.y - 50) + 'px)';
            u.el.style.opacity = '0';
            camadaNos.appendChild(u.el);
            void u.el.offsetWidth; // força o navegador a aplicar a posição inicial
            u.el.classList.remove('sem-transicao');
        }
        u.el.style.opacity = '1';
        if (!u.arrastando) u.el.style.transform = t;
    }

    function desenhar(op) {
        op = op || {};

        // ---- 1) Onde cada nó deve ficar ----
        var lista = layoutEstrutura();
        var alvos = {}, todos = [], estruturais = {};

        lista.forEach(function (it) {
            alvos[it.no.id] = { x: it.x, y: it.y };
            estruturais[it.no.id] = true;
            todos.push(it.no);
        });
        (op.soltos || []).forEach(function (s) {
            alvos[s.no.id] = { x: s.x, y: s.y };
            todos.push(s.no);
        });

        // posições definidas pelo usuário (arrastando) têm prioridade
        Object.keys(manual).forEach(function (id) {
            if (estruturais[id]) alvos[id] = manual[id];
        });
        if (op.flutuando && alvos[op.flutuando]) alvos[op.flutuando] = deslocar(alvos[op.flutuando], -1);
        if (op.descolado && alvos[op.descolado]) alvos[op.descolado] = deslocar(alvos[op.descolado], 1);

        posicoes = alvos;

        // ---- 2) Ligações (antes dos nós: os campos • / ∅ dependem delas) ----
        linksAtuais = calcularLinks(op);

        if (op.atual && ultimoAtual && op.atual !== ultimoAtual) {
            // a linha percorrida entre o "atual" anterior e o novo acende
            linksAtuais.forEach(function (l) {
                if ((l.de === ultimoAtual && l.para === op.atual) ||
                    (l.para === ultimoAtual && l.de === op.atual)) {
                    brilhos[chaveLink(l)] = performance.now() + 750;
                }
            });
        }
        ultimoAtual = op.atual || null;

        var saindoDe = {};
        linksAtuais.forEach(function (l) {
            saindoDe[l.de + ':' + l.tipo] = true;
        });

        // ---- 3) Etiquetas de ponteiro ----
        var ponteiros = fotoPonteiros();
        if (op.ponteiros) {
            Object.keys(op.ponteiros).forEach(function (k) { ponteiros[k] = op.ponteiros[k]; });
        }
        var tags = {};
        function addTag(id, nome, classe) {
            if (id === null || id === undefined) return;
            (tags[id] = tags[id] || []).push({ nome: nome, classe: classe || '' });
        }
        Object.keys(ponteiros).forEach(function (nome) { addTag(ponteiros[nome], nome); });
        if (op.tags) {
            Object.keys(op.tags).forEach(function (id) {
                op.tags[id].forEach(function (nome) { addTag(+id, nome, 'sim-tag--extra'); });
            });
        }
        if (op.atual) addTag(op.atual, 'atual', 'sim-tag--atual');

        // ---- 4) Cria / atualiza / posiciona os elementos ----
        var vivos = {};
        var indice = {};
        nos.forEach(function (n, i) { indice[n.id] = i; });

        todos.forEach(function (no) {
            var u = unidades[no.id];
            var nova = false;
            if (!u) {
                u = criarUnidade(no);
                unidades[no.id] = u;
                nova = true;
            }
            u.no = no;
            vivos[no.id] = true;

            var cl = u.el.classList;
            cl.toggle('atual',    op.atual  === no.id);
            cl.toggle('novo',     op.novo   === no.id);
            cl.toggle('alvo',     op.alvo   === no.id);
            cl.toggle('achado',   op.achado === no.id);
            cl.toggle('visitado', !!(op.visitados && op.visitados.indexOf(no.id) >= 0));
            cl.toggle('fixado',   !!manual[no.id]);

            // valor (pisca quando muda — ex.: cópia do sucessor na árvore)
            if (u.valorMostrado !== no.valor) {
                if (u.valorMostrado !== null) {
                    u.valorEl.classList.remove('trocou');
                    void u.valorEl.offsetWidth;
                    u.valorEl.classList.add('trocou');
                }
                u.valorEl.textContent = no.valor;
                u.valorMostrado = no.valor;
            }

            // campos de ponteiro: • aponta para alguém, ∅ = null
            var campos = u.el.querySelectorAll('.sim-ptr');
            for (var c = 0; c < campos.length; c++) {
                var tipo = campos[c].dataset.ptr;
                var tem = !!saindoDe[no.id + ':' + tipo];
                campos[c].textContent = tem ? '•' : '∅';
                campos[c].classList.toggle('nulo', !tem);
            }

            var html = (tags[no.id] || []).map(function (t) {
                return '<span class="sim-tag ' + t.classe + '">' + t.nome + '</span>';
            }).join('');
            if (u.tagsEl.innerHTML !== html) u.tagsEl.innerHTML = html;

            var idx = u.el.querySelector('.sim-indice');
            if (idx) idx.textContent = (indice[no.id] !== undefined) ? '[' + indice[no.id] + ']' : '';

            posicionar(u, alvos[no.id], nova);
        });

        // remove da tela quem saiu da estrutura
        Object.keys(unidades).forEach(function (id) {
            if (!vivos[id] && !unidades[id].saindo) {
                unidades[id].el.remove();
                delete unidades[id];
            }
        });

        vazio.hidden = todos.length > 0;
        atualizarInfo(ponteiros);

        // ---- 5) Câmera acompanha a estrutura ----
        var chave = lista.map(function (it) { return it.x + ',' + it.y; }).join('|');
        if (autoEnquadrar && chave !== ultimaChaveLayout) {
            ultimaChaveLayout = chave;
            enquadrar(true);
        }
    }

    function calcularLinks(op) {
        var L = [];
        if (ARVORE) {
            (function rec(n) {
                if (!n) return;
                if (n.esq) L.push({ de: n.id, para: n.esq.id, tipo: 'esq' });
                if (n.dir) L.push({ de: n.id, para: n.dir.id, tipo: 'dir' });
                rec(n.esq); rec(n.dir);
            })(raiz);
        } else {
            var fora = op.fora || [];
            var cadeia = nos.filter(function (n) { return fora.indexOf(n.id) < 0; });
            for (var i = 0; i < cadeia.length - 1; i++) {
                L.push({ de: cadeia[i].id, para: cadeia[i + 1].id, tipo: 'prox' });
                if (DUPLA) L.push({ de: cadeia[i + 1].id, para: cadeia[i].id, tipo: 'ant' });
            }
        }
        if (op.ocultar) {
            L = L.filter(function (l) {
                return !op.ocultar.some(function (o) {
                    return o.de === l.de && o.para === l.para && o.tipo === l.tipo;
                });
            });
        }
        (op.extras || []).forEach(function (e) {
            L.push({ de: e.de, para: e.para, tipo: e.tipo, formando: true });
        });
        return L;
    }

    function atualizarInfo(ponteiros) {
        var total = totalNos();
        var partes = [(ARVORE ? '<b>nós</b> = ' : '<b>tamanho</b> = ') + total];
        if (ARVORE) partes.push('<b>altura</b> = ' + alturaArvore(raiz));
        Object.keys(ponteiros).forEach(function (nome) {
            var u = unidades[ponteiros[nome]];
            partes.push('<b>' + nome + '</b> → ' + (u ? u.no.valor : 'null'));
        });
        info.innerHTML = partes.join('<span class="sep">·</span>');
    }

    /* =============================================================
       6. LIGAÇÕES (SVG) — redesenhadas a cada quadro
       ============================================================= */
    var SVGNS = 'http://www.w3.org/2000/svg';
    var caminhos = {}; // chave -> elemento <path>

    function chaveLink(l) { return l.de + '-' + l.para + '-' + l.tipo; }

    function centroDe(u) {
        var m = new DOMMatrixReadOnly(getComputedStyle(u.el).transform);
        return { x: m.m41, y: m.m42, hw: u.caixa.offsetWidth / 2, hh: u.caixa.offsetHeight / 2 };
    }

    // ponto na borda de um retângulo, saindo do centro na direção (dx, dy)
    function naBorda(c, dx, dy, folga) {
        var tx = dx !== 0 ? (c.hw + folga) / Math.abs(dx) : Infinity;
        var ty = dy !== 0 ? (c.hh + folga) / Math.abs(dy) : Infinity;
        var t = Math.min(tx, ty);
        return { x: c.x + dx * t, y: c.y + dy * t };
    }

    function quadro() {
        var agora = performance.now();
        var usados = {};

        linksAtuais.forEach(function (l) {
            var a = unidades[l.de], b = unidades[l.para];
            if (!a || !b) return;
            var ca = centroDe(a), cb = centroDe(b);
            var dx = cb.x - ca.x, dy = cb.y - ca.y;
            var dist = Math.hypot(dx, dy);
            if (dist < 12) return;
            dx /= dist; dy /= dist;

            // na lista dupla, ida e volta ficam lado a lado
            var off = 0;
            if (l.tipo === 'prox' && DUPLA) off = -7;
            if (l.tipo === 'ant') off = -7;
            var nx = -dy * off, ny = dx * off;

            var p1 = naBorda(ca, dx, dy, 3);
            var p2 = naBorda(cb, -dx, -dy, 5);
            p1.x += nx; p1.y += ny; p2.x += nx; p2.y += ny;

            var chave = chaveLink(l);
            usados[chave] = true;
            var p = caminhos[chave];
            if (!p) {
                p = document.createElementNS(SVGNS, 'path');
                p.setAttribute('pathLength', '100');
                svg.appendChild(p);
                caminhos[chave] = p;
            }
            p.setAttribute('d', 'M' + p1.x.toFixed(1) + ',' + p1.y.toFixed(1) +
                                ' L' + p2.x.toFixed(1) + ',' + p2.y.toFixed(1));

            var classe = 'sim-link sim-link--' + l.tipo +
                (l.formando ? ' formando' : '') +
                (brilhos[chave] > agora ? ' percorrido' : '') +
                ((a.saindo || b.saindo) ? ' sumindo' : '');
            if (p._classe !== classe) {
                p.setAttribute('class', classe);
                p._classe = classe;
            }
        });

        Object.keys(caminhos).forEach(function (k) {
            if (!usados[k]) {
                caminhos[k].remove();
                delete caminhos[k];
            }
        });

        requestAnimationFrame(quadro);
    }

    /* =============================================================
       7. PASSOS REUTILIZÁVEIS DAS OPERAÇÕES
       ============================================================= */

    // um passo: destaca a linha, explica e redesenha
    function passo(linha, texto, op) {
        if (linha !== null) destacarLinha(linha);
        narrar(texto);
        desenhar(op);
        return espera();
    }

    // coloca um nó na posição i da estrutura linear
    function colocar(i, no, linha, texto, op) {
        nos.splice(i, 0, no);
        op = op || {};
        if (op.novo === undefined) op.novo = no.id;
        return passo(linha, texto, op);
    }

    // anima o sumiço de um nó e tira da tela
    function sumir(id) {
        var u = unidades[id];
        if (!u) return Promise.resolve();
        u.saindo = true;
        u.el.classList.add('saindo');
        return espera(380).then(function () {
            u.el.remove();
            delete unidades[id];
        });
    }

    // retira o nó da posição i: fica vermelho, desliga, cai e some
    async function retirar(i, linha, texto, op) {
        var id = nos[i].id;
        op = mesclar(op || {}, { alvo: id });
        await passo(linha, texto, op);
        desenhar(mesclar(op, { fora: (op.fora || []).concat([id]), descolado: id }));
        await espera(520);
        await sumir(id);
        nos.splice(i, 1);
        delete manual[id];
        desenhar();
    }

    function delegar(opId, a, b) {
        mostrarCodigo(opId);
        return OPS[opId].fn(a, b);
    }

    // nó novo da árvore "carregado" ao lado do nó atual enquanto desce
    function aoLado(noNovo, idRef) {
        var p = posicoes[idRef] || { x: 0, y: 0 };
        return [{ no: noNovo, x: p.x - 78, y: p.y - 72 }];
    }

    // nó novo da lista "carregado" acima do nó atual
    function acima(noNovo, idRef) {
        var p = posicoes[idRef] || { x: 0, y: 0 };
        return [{ no: noNovo, x: p.x, y: p.y - 92 }];
    }

    /* =============================================================
       8. OPERAÇÕES
       campos: entradas usadas ('valor', 'pos', 'prio')
       grupo:  'add' | 'rem' | 'ver'  (cor do botão)
       fn devolve { msg, manter } — manter = deixa o destaque final
       ============================================================= */
    var OPS = {

        /* ======================= LISTAS ======================= */
        inserirInicio: {
            rotulo: 'Inserir no início', metodo: 'InserirInicio()', custo: 'O(1)',
            campos: ['valor'], grupo: 'add',
            fn: async function (v) {
                var antes = fotoPonteiros();
                var no = criarNo(v);
                var solto = { ponteiros: antes, flutuando: no.id, fora: [no.id] };

                await colocar(0, no, 0, 'Criamos um novo nó com o valor ' + v + '. Ele ainda está solto, sem nenhuma ligação.', solto);

                var tinha = nos.length > 1;
                var ex = tinha ? [{ de: no.id, para: nos[1].id, tipo: 'prox' }] : [];
                await passo(1, tinha
                    ? 'novo.Proximo passa a apontar para a cabeça atual (' + nos[1].valor + ').'
                    : 'A lista estava vazia: novo.Proximo continua null.',
                    mesclar(solto, { novo: no.id, extras: ex }));

                if (DUPLA) {
                    if (tinha) ex = ex.concat([{ de: nos[1].id, para: no.id, tipo: 'ant' }]);
                    await passo(2, tinha
                        ? 'O antigo primeiro nó (' + nos[1].valor + ') aponta de volta para o novo pelo Anterior.'
                        : 'Não existe antiga cabeça para ajustar o Anterior.',
                        mesclar(solto, { novo: no.id, extras: ex }));
                }

                await passo(DUPLA ? 3 : 2, 'A cabeça agora aponta para o nó ' + v + ', que se encaixa na lista.' +
                    (DUPLA && !tinha ? ' Como estava vazia, a cauda também.' : ''), { novo: no.id });
                return { msg: 'Inseriu ' + v + ' no início' };
            }
        },

        inserirFim: {
            rotulo: 'Inserir no fim', metodo: 'InserirFim()', custo: DUPLA ? 'O(1)' : 'O(n)',
            campos: ['valor'], grupo: 'add',
            fn: async function (v) {
                var no = criarNo(v);
                var n = nos.length;

                if (n === 0) {
                    await passo(0, 'Criamos o nó ' + v + '.', { soltos: [{ no: no, x: 0, y: -92 }] });
                    await colocar(0, no, 1, 'A lista está vazia: o novo nó vira a cabeça' + (DUPLA ? ' e a cauda.' : '.'));
                    return { msg: 'Inseriu ' + v + ' no fim' };
                }

                if (DUPLA) {
                    var antes = fotoPonteiros();
                    var cauda = nos[n - 1];
                    var solto = { ponteiros: antes, flutuando: no.id, fora: [no.id] };
                    await colocar(n, no, 0, 'Criamos o nó ' + v + '. Graças à cauda, não precisamos percorrer nada!', solto);
                    await passo(2, 'novo.Anterior aponta para a cauda atual (' + cauda.valor + ').',
                        mesclar(solto, { novo: no.id, extras: [{ de: no.id, para: cauda.id, tipo: 'ant' }] }));
                    await passo(3, 'cauda.Proximo (' + cauda.valor + ') agora aponta para o novo nó, que se encaixa.',
                        { novo: no.id, ponteiros: antes });
                    await passo(4, 'A cauda passa a ser o nó ' + v + '.', { novo: no.id });
                    return { msg: 'Inseriu ' + v + ' no fim' };
                }

                // lista simples: o nó novo é "carregado" enquanto procuramos o fim
                await passo(0, 'Criamos o nó ' + v + '. Agora é preciso achar o último nó.', { soltos: acima(no, nos[0].id) });
                await passo(2, 'atual começa na cabeça (' + nos[0].valor + ').',
                    { atual: nos[0].id, soltos: acima(no, nos[0].id) });
                var i = 0;
                while (i < n - 1) {
                    await passo(3, 'atual.Proximo não é null: ' + nos[i].valor + ' ainda não é o último.',
                        { atual: nos[i].id, soltos: acima(no, nos[i].id) });
                    i++;
                    await passo(4, 'atual avança para ' + nos[i].valor + '.',
                        { atual: nos[i].id, soltos: acima(no, nos[i].id) });
                }
                await passo(3, 'atual.Proximo é null: ' + nos[i].valor + ' é o último nó.',
                    { atual: nos[i].id, soltos: acima(no, nos[i].id) });
                var ultimo = nos[i];
                await colocar(n, no, 5, 'O último nó (' + ultimo.valor + ') passa a apontar para o novo nó ' + v + '.',
                    { atual: ultimo.id });
                return { msg: 'Inseriu ' + v + ' no fim (percorreu ' + n + ' nós)' };
            }
        },

        inserirPosicao: {
            rotulo: 'Inserir na posição', metodo: 'InserirNaPosicao()', custo: 'O(n)',
            campos: ['valor', 'pos'], grupo: 'add',
            fn: async function (v, p) {
                var n = nos.length;
                if (p < 0 || p > n) falhar('Posição inválida! Use um número de 0 a ' + n + '.');
                if (p === 0) {
                    await passo(0, 'pos é 0: é o mesmo que inserir no início.');
                    return delegar('inserirInicio', v);
                }
                if (DUPLA && p === n) {
                    await passo(1, 'pos é igual ao tamanho: é o mesmo que inserir no fim.');
                    return delegar('inserirFim', v);
                }

                var no = criarNo(v);
                var solto;

                if (!DUPLA) {
                    await passo(1, 'atual começa na cabeça (posição 0).', { atual: nos[0].id });
                    for (var i = 0; i < p - 1; i++) {
                        await passo(2, 'i = ' + i + ' < ' + (p - 1) + ': ainda não chegamos ao nó anterior à posição.', { atual: nos[i].id });
                        await passo(3, 'atual avança para ' + nos[i + 1].valor + '.', { atual: nos[i + 1].id });
                    }
                    var a = nos[p - 1];
                    var depois = nos[p];
                    solto = { atual: a.id, flutuando: no.id, fora: [no.id] };
                    await colocar(p, no, 4, 'Criamos o novo nó ' + v + ' logo depois de ' + a.valor + ' (ainda solto).', solto);
                    await passo(5, depois
                        ? 'novo.Proximo aponta para ' + depois.valor + ' (o antigo atual.Proximo).'
                        : 'atual.Proximo era null, então novo.Proximo também fica null.',
                        mesclar(solto, { novo: no.id, extras: depois ? [{ de: no.id, para: depois.id, tipo: 'prox' }] : [] }));
                    await passo(6, a.valor + '.Proximo agora aponta para o novo nó. Encaixado!', { novo: no.id });
                    return { msg: 'Inseriu ' + v + ' na posição ' + p };
                }

                await passo(2, 'atual começa na cabeça.', { atual: nos[0].id });
                for (var j = 0; j < p; j++) {
                    await passo(3, 'i = ' + j + ' < ' + p + ': continua.', { atual: nos[j].id });
                    await passo(4, 'atual avança para ' + nos[j + 1].valor + '.', { atual: nos[j + 1].id });
                }
                var alvo = nos[p], ant = nos[p - 1];
                solto = { atual: alvo.id, flutuando: no.id, fora: [no.id] };
                await colocar(p, no, 5, 'Criamos o nó ' + v + ', que vai ficar entre ' + ant.valor + ' e ' + alvo.valor + '.', solto);
                var ex = [{ de: no.id, para: alvo.id, tipo: 'prox' }, { de: no.id, para: ant.id, tipo: 'ant' }];
                await passo(6, 'O novo aponta para frente (' + alvo.valor + ') e para trás (' + ant.valor + ').',
                    mesclar(solto, { novo: no.id, extras: ex }));
                await passo(7, ant.valor + '.Proximo agora aponta para o novo nó.',
                    mesclar(solto, {
                        novo: no.id,
                        ocultar: [{ de: ant.id, para: alvo.id, tipo: 'prox' }],
                        extras: ex.concat([{ de: ant.id, para: no.id, tipo: 'prox' }])
                    }));
                await passo(8, alvo.valor + '.Anterior agora aponta para o novo nó. Encaixado nos dois sentidos!', { novo: no.id });
                return { msg: 'Inseriu ' + v + ' na posição ' + p };
            }
        },

        removerInicio: {
            rotulo: 'Remover do início', metodo: 'RemoverInicio()', custo: 'O(1)',
            campos: [], grupo: 'rem',
            fn: async function () {
                if (!nos.length) { destacarLinha(0); falhar('A lista está vazia: não há o que remover.'); }
                var v = nos[0].valor;
                await passo(0, 'A lista não está vazia.', { alvo: nos[0].id });
                await retirar(0, 1, 'A cabeça pula para o próximo nó. O nó ' + v + ' fica sem referência e o coletor de lixo o descarta.');
                if (DUPLA) {
                    if (nos.length) await passo(2, 'A nova cabeça (' + nos[0].valor + ') fica com Anterior = null.');
                    else await passo(3, 'A lista ficou vazia: a cauda também vira null.');
                }
                return { msg: 'Removeu ' + v + ' do início' };
            }
        },

        removerFim: {
            rotulo: 'Remover do fim', metodo: 'RemoverFim()', custo: DUPLA ? 'O(1)' : 'O(n)',
            campos: [], grupo: 'rem',
            fn: async function () {
                var n = nos.length;
                if (!n) { destacarLinha(0); falhar('A lista está vazia: não há o que remover.'); }
                var v = nos[n - 1].valor;

                if (DUPLA) {
                    await passo(0, 'A lista não está vazia.', { alvo: nos[n - 1].id });
                    await retirar(n - 1, 1, 'A cauda volta um nó usando o Anterior — sem percorrer nada!');
                    if (nos.length) await passo(2, 'A nova cauda (' + nos[nos.length - 1].valor + ') fica com Proximo = null.');
                    else await passo(3, 'A lista ficou vazia: a cabeça também vira null.');
                    return { msg: 'Removeu ' + v + ' do fim' };
                }

                await passo(0, 'A lista não está vazia.');
                if (n === 1) {
                    await retirar(0, 1, 'Só existe um nó: a cabeça vira null.');
                    return { msg: 'Removeu ' + v + ' do fim' };
                }
                await passo(2, 'Precisamos achar o PENÚLTIMO nó. atual começa na cabeça.', { atual: nos[0].id });
                var i = 0;
                while (i < n - 2) {
                    await passo(3, 'atual.Proximo.Proximo não é null: continua.', { atual: nos[i].id });
                    i++;
                    await passo(4, 'atual avança para ' + nos[i].valor + '.', { atual: nos[i].id });
                }
                await passo(3, nos[i].valor + ' é o penúltimo nó.', { atual: nos[i].id });
                await retirar(i + 1, 5, 'atual.Proximo = null: o último nó (' + v + ') é desligado.', { atual: nos[i].id });
                return { msg: 'Removeu ' + v + ' do fim (percorreu ' + (n - 1) + ' nós)' };
            }
        },

        removerValor: {
            rotulo: 'Remover valor', metodo: 'Remover(valor)', custo: 'O(n)',
            campos: ['valor'], grupo: 'rem',
            fn: async function (v) {
                var n = nos.length, i;

                if (DUPLA) {
                    if (!n) { destacarLinha(0); falhar('A lista está vazia.'); }
                    await passo(0, 'atual começa na cabeça.', { atual: nos[0].id });
                    i = 0;
                    while (i < n && nos[i].valor !== v) {
                        await passo(1, nos[i].valor + ' ≠ ' + v + ': continua procurando.', { atual: nos[i].id });
                        i++;
                        await passo(2, i < n ? 'atual avança para ' + nos[i].valor + '.' : 'atual virou null: fim da lista.',
                            i < n ? { atual: nos[i].id } : {});
                    }
                    if (i === n) {
                        await passo(3, 'O valor ' + v + ' não está na lista. Nada foi removido.');
                        return { msg: 'Valor ' + v + ' não encontrado' };
                    }
                    return removerDuploEm(i, 1, 'Achamos o nó ' + v + '!', 4);
                }

                if (!n) { destacarLinha(0); falhar('A lista está vazia: não há o que remover.'); }
                if (nos[0].valor === v) {
                    await passo(1, 'A própria cabeça tem o valor ' + v + '!', { alvo: nos[0].id });
                    await retirar(0, 1, 'A cabeça pula para o próximo nó.');
                    return { msg: 'Removeu o valor ' + v };
                }
                await passo(1, 'A cabeça (' + nos[0].valor + ') não é o valor procurado.', { atual: nos[0].id });
                await passo(2, 'atual começa na cabeça. Vamos olhar sempre o PRÓXIMO de atual.', { atual: nos[0].id });
                i = 0;
                while (i < n - 1 && nos[i + 1].valor !== v) {
                    await passo(3, 'atual.Proximo vale ' + nos[i + 1].valor + ' ≠ ' + v + '.', { atual: nos[i].id });
                    i++;
                    await passo(4, 'atual avança para ' + nos[i].valor + '.', { atual: nos[i].id });
                }
                if (i === n - 1) {
                    await passo(3, 'atual.Proximo é null: chegamos ao fim.', { atual: nos[i].id });
                    await passo(5, 'O valor ' + v + ' não está na lista. Nada foi removido.');
                    return { msg: 'Valor ' + v + ' não encontrado' };
                }
                await passo(3, 'atual.Proximo tem o valor ' + v + '!', { atual: nos[i].id, alvo: nos[i + 1].id });
                await retirar(i + 1, 6, nos[i].valor + '.Proximo pula o nó ' + v + ' e aponta para ' +
                    (nos[i + 2] ? nos[i + 2].valor : 'null') + '.', { atual: nos[i].id });
                return { msg: 'Removeu o valor ' + v };
            }
        },

        removerPosicao: {
            rotulo: 'Remover da posição', metodo: 'RemoverNaPosicao()', custo: 'O(n)',
            campos: ['pos'], grupo: 'rem',
            fn: async function (_v, p) {
                var n = nos.length;
                if (!n) falhar('A lista está vazia: não há o que remover.');
                if (p < 0 || p >= n) falhar('Posição inválida! Use um número de 0 a ' + (n - 1) + '.');

                if (DUPLA) {
                    await passo(0, 'atual começa na cabeça.', { atual: nos[0].id });
                    for (var j = 0; j < p; j++) {
                        await passo(1, 'i = ' + j + ' < ' + p + ': continua.', { atual: nos[j].id });
                        await passo(2, 'atual avança para ' + nos[j + 1].valor + '.', { atual: nos[j + 1].id });
                    }
                    return removerDuploEm(p, 1, 'atual está na posição ' + p + ' (' + nos[p].valor + ').', 3);
                }

                var v = nos[p].valor;
                if (p === 0) {
                    await retirar(0, 0, 'pos é 0: a cabeça pula para o próximo nó.');
                    return { msg: 'Removeu a posição 0 (' + v + ')' };
                }
                await passo(1, 'atual começa na cabeça.', { atual: nos[0].id });
                for (var i = 0; i < p - 1; i++) {
                    await passo(2, 'i = ' + i + ' < ' + (p - 1) + ': continua.', { atual: nos[i].id });
                    await passo(3, 'atual avança para ' + nos[i + 1].valor + '.', { atual: nos[i + 1].id });
                }
                await passo(4, 'O alvo é o próximo de atual: o nó ' + v + '.', { atual: nos[p - 1].id, alvo: nos[p].id });
                await retirar(p, 5, nos[p - 1].valor + '.Proximo pula o alvo e aponta para ' +
                    (nos[p + 1] ? nos[p + 1].valor : 'null') + '.', { atual: nos[p - 1].id });
                return { msg: 'Removeu a posição ' + p + ' (' + v + ')' };
            }
        },

        buscar: {
            rotulo: 'Buscar valor', metodo: 'Buscar(valor)', custo: 'O(n)',
            campos: ['valor'], grupo: 'ver',
            fn: async function (v) {
                var n = nos.length;
                await passo(0, 'atual começa na cabeça.', n ? { atual: nos[0].id } : {});
                for (var i = 0; i < n; i++) {
                    await passo(1, 'atual não é null: vamos comparar.', { atual: nos[i].id });
                    if (nos[i].valor === v) {
                        await passo(2, 'Encontrado! O valor ' + v + ' está na posição ' + i + ' (' + (i + 1) + ' comparações).', { achado: nos[i].id });
                        return { msg: 'Buscou ' + v + ': posição ' + i, manter: true };
                    }
                    await passo(2, nos[i].valor + ' ≠ ' + v + '.', { atual: nos[i].id });
                    await passo(3, i + 1 < n ? 'atual avança para ' + nos[i + 1].valor + '.' : 'atual avança para null.',
                        i + 1 < n ? { atual: nos[i + 1].id } : {});
                }
                await passo(1, 'atual é null: a lista acabou.');
                await passo(5, 'Retorna null: o valor ' + v + ' não está na lista.');
                return { msg: 'Buscou ' + v + ': não encontrado' };
            }
        },

        percorrerReverso: {
            rotulo: 'Percorrer de trás p/ frente', metodo: 'ExibirReverso()', custo: 'O(n)',
            campos: [], grupo: 'ver',
            fn: async function () {
                var n = nos.length;
                if (!n) { destacarLinha(0); falhar('A lista está vazia: a cauda é null.'); }
                var saida = [], vis = [];
                await passo(0, 'atual começa na CAUDA (' + nos[n - 1].valor + ').', { atual: nos[n - 1].id });
                for (var i = n - 1; i >= 0; i--) {
                    saida.push(nos[i].valor);
                    vis.push(nos[i].id);
                    await passo(2, 'Imprime ' + nos[i].valor + '.   Saída: ' + saida.join(' '), { atual: nos[i].id, visitados: vis.slice() });
                    await passo(3, i > 0 ? 'Volta pelo Anterior até ' + nos[i - 1].valor + '.' : 'Anterior é null: terminou.',
                        i > 0 ? { atual: nos[i - 1].id, visitados: vis.slice() } : { visitados: vis.slice() });
                }
                narrar('Saída final: ' + saida.join(' '));
                return { msg: 'Percorreu ao contrário: ' + saida.join(' '), manter: true };
            }
        },

        /* ======================= PILHA ======================= */
        empilhar: {
            rotulo: 'Empilhar (Push)', metodo: 'Push()', custo: 'O(1)',
            campos: ['valor'], grupo: 'add',
            fn: async function (v) {
                var antes = fotoPonteiros();
                var no = criarNo(v);
                var solto = { ponteiros: antes, flutuando: no.id, fora: [no.id] };
                await colocar(0, no, 0, 'Criamos um novo nó com o valor ' + v + ' (ainda solto, ao lado da pilha).', solto);
                await passo(1, nos.length > 1
                    ? 'novo.Proximo aponta para o topo atual (' + nos[1].valor + ').'
                    : 'A pilha estava vazia: novo.Proximo fica null.',
                    mesclar(solto, { novo: no.id, extras: nos.length > 1 ? [{ de: no.id, para: nos[1].id, tipo: 'prox' }] : [] }));
                await passo(2, 'O topo agora é o nó ' + v + ', que entra na pilha.', { novo: no.id });
                await passo(3, 'tamanho passa para ' + nos.length + '.', { novo: no.id });
                return { msg: 'Push(' + v + ')' };
            }
        },

        desempilhar: {
            rotulo: 'Desempilhar (Pop)', metodo: 'Pop()', custo: 'O(1)',
            campos: [], grupo: 'rem',
            fn: async function () {
                if (!nos.length) { destacarLinha(0); falhar('Pilha vazia! Tentar desempilhar gera underflow.'); }
                var v = nos[0].valor;
                await passo(0, 'A pilha não está vazia.', { alvo: nos[0].id });
                await passo(1, 'Guardamos o valor do topo: ' + v + '.', { alvo: nos[0].id });
                await retirar(0, 2, 'O topo desce para o próximo nó.');
                await passo(3, 'tamanho passa para ' + nos.length + '.');
                await passo(4, 'Retorna ' + v + '.');
                return { msg: 'Pop() → ' + v };
            }
        },

        espiar: {
            rotulo: 'Ver topo (Peek)', metodo: 'Peek()', custo: 'O(1)',
            campos: [], grupo: 'ver',
            fn: async function () {
                if (!nos.length) { destacarLinha(0); falhar('Pilha vazia! Não há topo para olhar.'); }
                await passo(0, 'A pilha não está vazia.');
                await passo(1, 'Retorna ' + nos[0].valor + ' SEM remover nada.', { achado: nos[0].id });
                return { msg: 'Peek() → ' + nos[0].valor, manter: true };
            }
        },

        /* ======================= FILAS ======================= */
        enfileirar: {
            rotulo: 'Enfileirar (Enqueue)', metodo: 'Enfileirar()', custo: 'O(1)',
            campos: ['valor'], grupo: 'add',
            fn: async function (v) {
                var no = criarNo(v);
                var n = nos.length;
                if (!n) {
                    await passo(0, 'Criamos o nó ' + v + '.', { soltos: [{ no: no, x: 0, y: -92 }] });
                    await colocar(0, no, 1, 'A fila estava vazia: início e fim apontam para o novo nó ' + v + '.');
                    return { msg: 'Enfileirou ' + v };
                }
                var antes = fotoPonteiros();
                var fim = nos[n - 1];
                var solto = { ponteiros: antes, flutuando: no.id, fora: [no.id] };
                await colocar(n, no, 0, 'Criamos o novo nó ' + v + ' no fim da fila (ainda solto).', solto);
                await passo(2, 'O último da fila (' + fim.valor + ') passa a apontar para o novo.', { novo: no.id, ponteiros: antes });
                await passo(3, 'O ponteiro fim agora é o nó ' + v + '.', { novo: no.id });
                return { msg: 'Enfileirou ' + v };
            }
        },

        enfileirarPrio: {
            rotulo: 'Enfileirar com prioridade', metodo: 'Enfileirar(valor, prioridade)', custo: 'O(n)',
            campos: ['valor', 'prio'], grupo: 'add',
            fn: async function (v, pr) {
                var no = criarNo(v, pr);
                var n = nos.length;
                await passo(0, 'Criamos o nó ' + v + ' com prioridade ' + pr + '.',
                    { soltos: n ? acima(no, nos[0].id) : [{ no: no, x: 0, y: -92 }] });

                if (!n || pr > nos[0].prio) {
                    await passo(1, !n ? 'A fila está vazia.'
                        : 'P' + pr + ' é maior que a do início (P' + nos[0].prio + '): o novo fura a fila!',
                        { atual: n ? nos[0].id : null, soltos: n ? acima(no, nos[0].id) : [{ no: no, x: 0, y: -92 }] });
                    await colocar(0, no, 2, 'O novo nó vira o início da fila.', { flutuando: no.id, fora: [no.id] });
                    await passo(2, 'Encaixado no início!', { novo: no.id });
                    return { msg: 'Enfileirou ' + v + ' (P' + pr + ') no início' };
                }

                await passo(1, 'P' + pr + ' não supera a do início (P' + nos[0].prio + ').',
                    { atual: nos[0].id, soltos: acima(no, nos[0].id) });
                await passo(4, 'atual começa no início.', { atual: nos[0].id, soltos: acima(no, nos[0].id) });
                var i = 0;
                while (i < n - 1 && nos[i + 1].prio >= pr) {
                    await passo(5, 'O próximo tem P' + nos[i + 1].prio + ' ≥ P' + pr + ': ele fica na frente.',
                        { atual: nos[i].id, soltos: acima(no, nos[i].id) });
                    i++;
                    await passo(6, 'atual avança para ' + nos[i].valor + '.', { atual: nos[i].id, soltos: acima(no, nos[i].id) });
                }
                await passo(5, i < n - 1
                    ? 'O próximo tem P' + nos[i + 1].prio + ' < P' + pr + ': o novo entra aqui.'
                    : 'Chegamos ao fim: o novo vai para o final.', { atual: nos[i].id, soltos: acima(no, nos[i].id) });
                var a = nos[i];
                var depois = nos[i + 1];
                var solto = { atual: a.id, flutuando: no.id, fora: [no.id] };
                await colocar(i + 1, no, 7, depois ? 'novo.Proximo aponta para ' + depois.valor + '.' : 'novo.Proximo fica null.',
                    mesclar(solto, { extras: depois ? [{ de: no.id, para: depois.id, tipo: 'prox' }] : [] }));
                await passo(8, a.valor + '.Proximo agora aponta para o novo. Encaixado na ordem certa!', { novo: no.id });
                return { msg: 'Enfileirou ' + v + ' (P' + pr + ') na posição ' + (i + 1) };
            }
        },

        desenfileirar: {
            rotulo: 'Desenfileirar (Dequeue)', metodo: 'Desenfileirar()', custo: 'O(1)',
            campos: [], grupo: 'rem',
            fn: async function () {
                if (!nos.length) { destacarLinha(0); falhar('Fila vazia! Não há ninguém para atender.'); }
                var v = nos[0].valor;
                await passo(0, 'A fila não está vazia.', { alvo: nos[0].id });
                await passo(1, 'Guardamos o valor do início: ' + v + '.', { alvo: nos[0].id });
                await retirar(0, 2, 'O início avança para o próximo da fila.');
                if (!CONF.prioridade) {
                    await passo(3, nos.length ? 'A fila ainda tem elementos: o fim continua igual.' : 'A fila ficou vazia: fim também vira null.');
                }
                await passo(4, 'Retorna ' + v + '.');
                return { msg: 'Desenfileirou ' + v };
            }
        },

        frente: {
            rotulo: 'Ver início (Front)', metodo: 'Frente()', custo: 'O(1)',
            campos: [], grupo: 'ver',
            fn: async function () {
                if (!nos.length) { destacarLinha(0); falhar('Fila vazia! Não há ninguém na frente.'); }
                await passo(0, 'A fila não está vazia.');
                await passo(1, 'Retorna ' + nos[0].valor + ' SEM remover.', { achado: nos[0].id });
                return { msg: 'Frente → ' + nos[0].valor, manter: true };
            }
        },

        /* ======================= ÁRVORE BINÁRIA DE BUSCA ======================= */
        arvInserir: {
            rotulo: 'Inserir', metodo: 'Inserir(valor)', custo: 'O(h)',
            campos: ['valor'], grupo: 'add',
            fn: async function (v) {
                var no = criarNo(v);
                if (!raiz) {
                    await passo(0, 'Criamos o nó ' + v + '.', { soltos: [{ no: no, x: 0, y: -90 }] });
                    raiz = no;
                    await passo(1, 'A árvore estava vazia: o novo nó vira a raiz.', { novo: no.id });
                    return { msg: 'Inseriu ' + v + ' como raiz' };
                }

                await passo(0, 'Criamos o nó ' + v + '. Agora vamos descer a árvore procurando o lugar dele.', { soltos: aoLado(no, raiz.id) });
                var a = raiz;
                await passo(2, 'atual começa na raiz (' + a.valor + ').', { atual: a.id, soltos: aoLado(no, a.id) });
                var niveis = 1;

                while (true) {
                    await passo(3, 'Comparando ' + v + ' com ' + a.valor + '.', { atual: a.id, soltos: aoLado(no, a.id) });

                    if (v < a.valor) {
                        await passo(4, v + ' < ' + a.valor + ': o lugar dele é à ESQUERDA.', { atual: a.id, soltos: aoLado(no, a.id) });
                        if (!a.esq) {
                            a.esq = no;
                            await passo(5, 'A esquerda de ' + a.valor + ' está livre: o nó ' + v + ' entra aqui.', { novo: no.id, atual: a.id });
                            return { msg: 'Inseriu ' + v + ' (' + niveis + ' comparações)' };
                        }
                        a = a.esq; niveis++;
                        await passo(6, 'atual desce para ' + a.valor + '.', { atual: a.id, soltos: aoLado(no, a.id) });
                    } else if (v > a.valor) {
                        await passo(7, v + ' > ' + a.valor + ': o lugar dele é à DIREITA.', { atual: a.id, soltos: aoLado(no, a.id) });
                        if (!a.dir) {
                            a.dir = no;
                            await passo(8, 'A direita de ' + a.valor + ' está livre: o nó ' + v + ' entra aqui.', { novo: no.id, atual: a.id });
                            return { msg: 'Inseriu ' + v + ' (' + niveis + ' comparações)' };
                        }
                        a = a.dir; niveis++;
                        await passo(9, 'atual desce para ' + a.valor + '.', { atual: a.id, soltos: aoLado(no, a.id) });
                    } else {
                        await passo(10, 'O valor ' + v + ' já existe! Em uma árvore binária de busca não entram valores repetidos.',
                            { achado: a.id, soltos: aoLado(no, a.id) });
                        await sumir(no.id);
                        desenhar({ achado: a.id });
                        return { msg: 'Valor ' + v + ' já existia', manter: true };
                    }
                }
            }
        },

        arvBuscar: {
            rotulo: 'Buscar', metodo: 'Buscar(valor)', custo: 'O(h)',
            campos: ['valor'], grupo: 'ver',
            fn: async function (v) {
                var a = raiz, passos = 0;
                await passo(0, raiz ? 'atual começa na raiz (' + raiz.valor + ').' : 'A árvore está vazia: atual começa null.',
                    raiz ? { atual: raiz.id } : {});
                while (a) {
                    passos++;
                    await passo(1, 'atual não é null: vamos comparar ' + v + ' com ' + a.valor + '.', { atual: a.id });
                    if (v === a.valor) {
                        await passo(2, 'Encontrado em ' + passos + ' comparações! Repare que não visitamos a árvore inteira.', { achado: a.id });
                        return { msg: 'Buscou ' + v + ': encontrado (' + passos + ' comparações)', manter: true };
                    }
                    var antes = a.valor;
                    if (v < a.valor) {
                        a = a.esq;
                        await passo(3, v + ' < ' + antes + ': descartamos a subárvore direita inteira e descemos para a esquerda' +
                            (a ? ' (' + a.valor + ').' : ', que é null.'), a ? { atual: a.id } : {});
                    } else {
                        a = a.dir;
                        await passo(4, v + ' > ' + antes + ': descartamos a subárvore esquerda inteira e descemos para a direita' +
                            (a ? ' (' + a.valor + ').' : ', que é null.'), a ? { atual: a.id } : {});
                    }
                }
                await passo(6, 'Chegamos a null: o valor ' + v + ' não está na árvore.');
                return { msg: 'Buscou ' + v + ': não encontrado' };
            }
        },

        arvRemover: {
            rotulo: 'Remover', metodo: 'Remover(valor)', custo: 'O(h)',
            campos: ['valor'], grupo: 'rem',
            fn: async function (v) {
                if (!raiz) { destacarLinha(0); falhar('A árvore está vazia.'); }
                var pai = null, a = raiz;
                function tagPai() { var t = {}; if (pai) t[pai.id] = ['pai']; return t; }

                await passo(0, 'pai começa null e atual começa na raiz.', { atual: a.id });
                while (a && a.valor !== v) {
                    await passo(1, a.valor + ' ≠ ' + v + ': continua descendo.', { atual: a.id, tags: tagPai() });
                    pai = a;
                    a = v < a.valor ? a.esq : a.dir;
                    await passo(3, (v < pai.valor ? v + ' < ' + pai.valor + ': desce para a esquerda' : v + ' > ' + pai.valor + ': desce para a direita') +
                        (a ? ' (' + a.valor + ').' : ', que é null.'), a ? { atual: a.id, tags: tagPai() } : { tags: tagPai() });
                }
                if (!a) {
                    await passo(5, 'O valor ' + v + ' não está na árvore. Nada foi removido.');
                    return { msg: 'Valor ' + v + ' não encontrado' };
                }
                await passo(1, 'Achamos o nó ' + v + '!', { alvo: a.id, tags: tagPai() });

                // caso 3: dois filhos → troca pelo sucessor
                if (a.esq && a.dir) {
                    await passo(6, 'O nó tem DOIS filhos. Truque: trocamos o valor dele pelo sucessor (o menor da subárvore direita).', { alvo: a.id });
                    var paiSuc = a, suc = a.dir;
                    await passo(7, 'O sucessor começa no filho direito (' + suc.valor + ').', { alvo: a.id, atual: suc.id });
                    while (suc.esq) {
                        paiSuc = suc;
                        suc = suc.esq;
                        await passo(8, 'Ainda existe filho à esquerda: desce para ' + suc.valor + '.', { alvo: a.id, atual: suc.id });
                    }
                    await passo(9, suc.valor + ' é o sucessor. Copiamos esse valor para o nó ' + a.valor + '.', { alvo: a.id, achado: suc.id });
                    a.valor = suc.valor;
                    await passo(9, 'O nó agora vale ' + a.valor + ' e a ordem da árvore continua correta. Falta tirar o sucessor original.',
                        { novo: a.id, alvo: suc.id });
                    pai = paiSuc;
                    a = suc;
                    await passo(10, 'O problema virou remover o sucessor, que tem no máximo um filho (nunca tem filho à esquerda).',
                        { alvo: a.id, tags: tagPai() });
                }

                var filho = a.esq || a.dir;
                await passo(12, filho
                    ? 'O nó tem um filho (' + filho.valor + '), que vai subir para o lugar dele.'
                    : 'O nó é uma FOLHA: basta desligá-lo do pai.', { alvo: a.id, tags: tagPai() });

                var p = posicoes[a.id] || { x: 0, y: 0 };
                var linha = !pai ? 13 : (pai.esq === a ? 14 : 15);
                if (!pai) raiz = filho;
                else if (pai.esq === a) pai.esq = filho;
                else pai.dir = filho;
                delete manual[a.id];

                await passo(linha, !pai
                    ? 'Era a raiz: a raiz passa a ser ' + (filho ? filho.valor : 'null') + '.'
                    : 'O pai (' + pai.valor + ') passa a apontar para ' + (filho ? filho.valor : 'null') + '.',
                    { alvo: a.id, soltos: [{ no: a, x: p.x, y: p.y + 80 }] });
                await sumir(a.id);
                desenhar();
                return { msg: 'Removeu ' + v };
            }
        },

        arvEmOrdem:  { rotulo: 'Em ordem',  metodo: 'EmOrdem(raiz)',  custo: 'O(n)', campos: [], grupo: 'ver', fn: function () { return percurso('em'); } },
        arvPreOrdem: { rotulo: 'Pré-ordem', metodo: 'PreOrdem(raiz)', custo: 'O(n)', campos: [], grupo: 'ver', fn: function () { return percurso('pre'); } },
        arvPosOrdem: { rotulo: 'Pós-ordem', metodo: 'PosOrdem(raiz)', custo: 'O(n)', campos: [], grupo: 'ver', fn: function () { return percurso('pos'); } },

        arvNivel: {
            rotulo: 'Por nível (largura)', metodo: 'PorNivel()', custo: 'O(n)',
            campos: [], grupo: 'ver',
            fn: async function () {
                if (!raiz) falhar('A árvore está vazia.');
                var fila = [raiz], saida = [], vis = [];
                function txtFila() { return '[' + fila.map(function (n) { return n.valor; }).join(', ') + ']'; }
                await passo(0, 'Usamos uma FILA para visitar nível por nível.', {});
                await passo(1, 'A raiz entra na fila.   Fila: ' + txtFila(), { atual: raiz.id });
                while (fila.length) {
                    await passo(2, 'A fila não está vazia.   Fila: ' + txtFila(), { visitados: vis.slice() });
                    var n = fila.shift();
                    await passo(3, 'Sai da fila: ' + n.valor + '.   Fila: ' + txtFila(), { atual: n.id, visitados: vis.slice() });
                    saida.push(n.valor); vis.push(n.id);
                    await passo(4, 'Imprime ' + n.valor + '.   Saída: ' + saida.join(' '), { atual: n.id, visitados: vis.slice() });
                    if (n.esq) { fila.push(n.esq); await passo(5, 'Filho esquerdo (' + n.esq.valor + ') entra na fila.   Fila: ' + txtFila(), { atual: n.id, visitados: vis.slice() }); }
                    if (n.dir) { fila.push(n.dir); await passo(6, 'Filho direito (' + n.dir.valor + ') entra na fila.   Fila: ' + txtFila(), { atual: n.id, visitados: vis.slice() }); }
                }
                narrar('Saída por nível: ' + saida.join(' '));
                return { msg: 'Por nível: ' + saida.join(' '), manter: true };
            }
        },

        arvMinimo: {
            rotulo: 'Mínimo', metodo: 'Minimo()', custo: 'O(h)',
            campos: [], grupo: 'ver',
            fn: function () { return extremo('esq'); }
        },

        arvMaximo: {
            rotulo: 'Máximo', metodo: 'Maximo()', custo: 'O(h)',
            campos: [], grupo: 'ver',
            fn: function () { return extremo('dir'); }
        }
    };

    /* ---- Percursos em profundidade (recursivos) ---- */
    async function percurso(tipo) {
        if (!raiz) falhar('A árvore está vazia.');
        var saida = [], vis = [];
        var ordem = { pre: ['visita', 'esq', 'dir'], em: ['esq', 'visita', 'dir'], pos: ['esq', 'dir', 'visita'] }[tipo];
        var linhaDe = {};
        ordem.forEach(function (acao, i) { linhaDe[acao] = 2 + i; });

        async function rec(n) {
            await passo(1, 'Chamada em ' + n.valor + '.', { atual: n.id, visitados: vis.slice() });
            for (var k = 0; k < ordem.length; k++) {
                var acao = ordem[k];
                if (acao === 'visita') {
                    saida.push(n.valor); vis.push(n.id);
                    await passo(linhaDe.visita, 'Imprime ' + n.valor + '.   Saída: ' + saida.join(' '), { atual: n.id, visitados: vis.slice() });
                } else {
                    var filho = n[acao];
                    if (!filho) continue; // filho null: a chamada retorna na hora
                    await passo(linhaDe[acao], 'Desce para a ' + (acao === 'esq' ? 'esquerda' : 'direita') + ' de ' + n.valor + '.',
                        { atual: n.id, visitados: vis.slice() });
                    await rec(filho);
                    await passo(linhaDe[acao], 'Volta para ' + n.valor + '.', { atual: n.id, visitados: vis.slice() });
                }
            }
        }

        await rec(raiz);
        narrar('Saída final: ' + saida.join(' '));
        var nome = { em: 'Em ordem', pre: 'Pré-ordem', pos: 'Pós-ordem' }[tipo];
        return { msg: nome + ': ' + saida.join(' '), manter: true };
    }

    async function extremo(lado) {
        if (!raiz) falhar('A árvore está vazia.');
        var a = raiz;
        var nomeLado = lado === 'esq' ? 'esquerda' : 'direita';
        await passo(0, 'atual começa na raiz (' + a.valor + ').', { atual: a.id });
        while (a[lado]) {
            await passo(1, 'Ainda existe filho à ' + nomeLado + '.', { atual: a.id });
            a = a[lado];
            await passo(2, 'Desce para ' + a.valor + '.', { atual: a.id });
        }
        await passo(1, 'Não há mais filho à ' + nomeLado + '.', { atual: a.id });
        await passo(3, (lado === 'esq' ? 'O menor' : 'O maior') + ' valor é ' + a.valor + ': sempre o nó mais à ' + nomeLado + '.', { achado: a.id });
        return { msg: (lado === 'esq' ? 'Mínimo' : 'Máximo') + ' = ' + a.valor, manter: true };
    }

    /* ---- Lista dupla: remover o nó da posição i (usado por 2 operações) ---- */
    async function removerDuploEm(i, linhaAchou, textoAchou, linhaBase) {
        var no = nos[i], antN = nos[i - 1], proxN = nos[i + 1];
        await passo(linhaAchou, textoAchou, { alvo: no.id });
        await passo(antN ? linhaBase : linhaBase + 1, antN
            ? 'O anterior (' + antN.valor + ') passa a apontar para ' + (proxN ? proxN.valor : 'null') + '.'
            : 'Era a cabeça: a cabeça passa para ' + (proxN ? proxN.valor : 'null') + '.', { alvo: no.id });
        await retirar(i, proxN ? linhaBase + 2 : linhaBase + 3, proxN
            ? 'O próximo (' + proxN.valor + ') aponta de volta para ' + (antN ? antN.valor : 'null') + '. O nó sai da corrente.'
            : 'Era a cauda: a cauda volta para ' + (antN ? antN.valor : 'null') + '. O nó sai da corrente.');
        return { msg: 'Removeu ' + no.valor + ' (posição ' + i + ')' };
    }

    /* =============================================================
       9. EXECUÇÃO (lê os campos, valida e roda a operação)
       ============================================================= */
    function travar(sim) {
        app.classList.toggle('ocupado', sim);
        var btns = app.querySelectorAll('.sim-painel button');
        for (var i = 0; i < btns.length; i++) btns[i].disabled = sim;
    }

    function lerInteiro(campo) {
        if (!campo || campo.value.trim() === '') return null;
        var n = parseInt(campo.value, 10);
        return isNaN(n) ? null : n;
    }

    // preset: valores vindos do menu do botão direito (ignoram os campos)
    async function executar(opId, preset) {
        if (ocupado) return;
        preset = preset || {};
        var op = OPS[opId];
        var valor = null, pos = null, prio = null;

        if (op.campos.indexOf('valor') >= 0) {
            valor = (preset.valor !== undefined) ? preset.valor : lerInteiro(campoValor);
            if (valor === null) valor = sortear();          // vazio = sorteia
            if (valor < 0 || valor > 999) return erroRapido('O valor precisa estar entre 0 e 999.', campoValor);
        }
        if (op.campos.indexOf('pos') >= 0) {
            pos = (preset.pos !== undefined) ? preset.pos : lerInteiro(campoPos);
            if (pos === null) return erroRapido('Informe a posição (0 a ' + Math.max(nos.length - (op.grupo === 'rem' ? 1 : 0), 0) + ').', campoPos);
        }
        if (op.campos.indexOf('prio') >= 0) {
            prio = (preset.prio !== undefined) ? preset.prio : parseInt(campoPrio.value, 10);
        }
        if (op.grupo === 'add' && totalNos() >= CONF.max) {
            return erroRapido('Limite visual de ' + CONF.max + ' nós atingido. Remova algum ou clique em Limpar.');
        }

        ocupado = true;
        travar(true);
        fecharMenu();
        mostrarCodigo(opId);
        try {
            var segundo = op.campos.indexOf('pos') >= 0 ? pos : prio;
            var r = await op.fn(valor, segundo);
            registrar(r.msg);
            if (!r.manter) desenhar();
        } catch (e) {
            if (e instanceof Falha) {
                narrar(e.message, 'erro');
                registrar(e.message, true);
                desenhar();
            } else {
                throw e;
            }
        } finally {
            ocupado = false;
            liberarPasso = null;
            btnProximo.hidden = true;
            travar(false);
            if (campoValor && !preset.semFoco) campoValor.value = '';
        }
    }

    function erroRapido(msg, campo) {
        narrar(msg, 'erro');
        if (campo) {
            campo.classList.remove('tremer');
            void campo.offsetWidth;
            campo.classList.add('tremer');
            campo.focus();
        }
    }

    /* =============================================================
       10. CÂMERA: zoom, mover o fundo e enquadrar
       ============================================================= */
    var cam = { x: 0, y: 0, s: 1 };
    var autoEnquadrar = true;
    var ultimaChaveLayout = '';

    function aplicarCamera(suave) {
        mundo.classList.toggle('suave', !!suave);
        mundo.style.transform = 'translate(' + cam.x + 'px,' + cam.y + 'px) scale(' + cam.s + ')';
    }

    function limitar(v, a, b) { return Math.max(a, Math.min(b, v)); }

    function enquadrar(suave) {
        var lista = layoutEstrutura();
        var W = palco.clientWidth, H = palco.clientHeight;
        var topo = 64, baixo = 96; // espaço da barra de informações e da narração
        var areaH = Math.max(H - topo - baixo, 120);

        if (!lista.length) {
            cam = { x: W / 2, y: topo + areaH / 2, s: 1 };
            aplicarCamera(suave);
            return;
        }
        var minX = Infinity, maxX = -Infinity, minY = Infinity, maxY = -Infinity;
        lista.forEach(function (it) {
            minX = Math.min(minX, it.x); maxX = Math.max(maxX, it.x);
            minY = Math.min(minY, it.y); maxY = Math.max(maxY, it.y);
        });
        var larg = (maxX - minX) + 220;
        var alt  = (maxY - minY) + (CONF.forma === 'coluna' ? 120 : 200);
        var s = limitar(Math.min(W / larg, areaH / alt, 1.25), 0.3, 1.25);
        cam.s = s;
        cam.x = W / 2 - ((minX + maxX) / 2) * s;
        cam.y = topo + areaH / 2 - ((minY + maxY) / 2) * s + (CONF.forma === 'coluna' ? 0 : 14 * s);
        aplicarCamera(suave);
    }

    function zoomEm(px, py, fator, suave) {
        var ns = limitar(cam.s * fator, 0.25, 2.5);
        cam.x = px - (px - cam.x) * ns / cam.s;
        cam.y = py - (py - cam.y) * ns / cam.s;
        cam.s = ns;
        autoEnquadrar = false;
        aplicarCamera(suave);
    }

    palco.addEventListener('wheel', function (e) {
        e.preventDefault();
        var r = palco.getBoundingClientRect();
        zoomEm(e.clientX - r.left, e.clientY - r.top, Math.exp(-e.deltaY * 0.0015), false);
    }, { passive: false });

    pegar('sim-zoom-mais').addEventListener('click', function () {
        zoomEm(palco.clientWidth / 2, palco.clientHeight / 2, 1.25, true);
    });
    pegar('sim-zoom-menos').addEventListener('click', function () {
        zoomEm(palco.clientWidth / 2, palco.clientHeight / 2, 0.8, true);
    });
    pegar('sim-ajustar').addEventListener('click', function () {
        autoEnquadrar = true;
        enquadrar(true);
    });
    pegar('sim-reorganizar').addEventListener('click', reorganizar);

    function reorganizar() {
        manual = {};
        autoEnquadrar = true;
        desenhar();
        enquadrar(true);
        narrar('Os nós voltaram para a posição automática.');
    }

    // mover o fundo (arrastando onde não há nó)
    var pan = null;
    palco.addEventListener('pointerdown', function (e) {
        if (e.button !== 0) return;
        if (e.target.closest('.sim-ancora, .sim-narracao, .sim-dica, .sim-info, .sim-menu')) return;
        pan = { x: e.clientX, y: e.clientY, cx: cam.x, cy: cam.y };
        palco.setPointerCapture(e.pointerId);
        palco.classList.add('movendo');
        fecharMenu();
    });
    palco.addEventListener('pointermove', function (e) {
        if (!pan) return;
        cam.x = pan.cx + (e.clientX - pan.x);
        cam.y = pan.cy + (e.clientY - pan.y);
        if (Math.abs(e.clientX - pan.x) + Math.abs(e.clientY - pan.y) > 3) autoEnquadrar = false;
        aplicarCamera(false);
    });
    function soltarPan() {
        pan = null;
        palco.classList.remove('movendo');
    }
    palco.addEventListener('pointerup', soltarPan);
    palco.addEventListener('pointercancel', soltarPan);

    /* =============================================================
       11. ARRASTAR NÓS
       ============================================================= */
    function ligarArraste(u) {
        var inicio = null;

        u.el.addEventListener('pointerdown', function (e) {
            if (e.button !== 0) return;
            e.stopPropagation();
            fecharMenu();
            var p = posicoes[u.no.id] || { x: 0, y: 0 };
            inicio = { mx: e.clientX, my: e.clientY, x: p.x, y: p.y, moveu: false };
            u.el.setPointerCapture(e.pointerId);
        });

        u.el.addEventListener('pointermove', function (e) {
            if (!inicio) return;
            var dx = (e.clientX - inicio.mx) / cam.s;
            var dy = (e.clientY - inicio.my) / cam.s;
            if (!inicio.moveu && Math.abs(dx) + Math.abs(dy) < 4) return;
            inicio.moveu = true;
            u.arrastando = true;
            u.el.classList.add('arrastando');
            var nx = inicio.x + dx, ny = inicio.y + dy;
            u.el.style.transform = 'translate(' + nx + 'px,' + ny + 'px)';
            manual[u.no.id] = { x: nx, y: ny };
            posicoes[u.no.id] = manual[u.no.id];
        });

        function fim() {
            if (!inicio) return;
            var moveu = inicio.moveu;
            inicio = null;
            u.arrastando = false;
            u.el.classList.remove('arrastando');
            if (moveu) {
                u.el.classList.add('fixado');
                if (!ocupado) narrar('Nó ' + u.no.valor + ' movido. Clique com o botão direito para soltá-lo ou use "Reorganizar".');
            }
        }
        u.el.addEventListener('pointerup', fim);
        u.el.addEventListener('pointercancel', fim);
    }

    /* =============================================================
       12. MENU DO BOTÃO DIREITO
       item = { texto, dica, acao, desativado, perigo } | '-' | { titulo }
       ============================================================= */
    function valorDoCampo() {
        var v = lerInteiro(campoValor);
        return v === null ? undefined : v;
    }

    function abrirMenu(x, y, itens) {
        menu.innerHTML = '';
        itens.forEach(function (it) {
            if (it === '-') {
                menu.appendChild(document.createElement('hr'));
                return;
            }
            if (it.titulo) {
                var t = document.createElement('div');
                t.className = 'sim-menu-titulo';
                t.textContent = it.titulo;
                menu.appendChild(t);
                return;
            }
            if (it.editar) {
                menu.appendChild(it.editar);
                return;
            }
            var b = document.createElement('button');
            b.type = 'button';
            b.className = 'sim-menu-item' + (it.perigo ? ' perigo' : '') + (it.cor ? ' ' + it.cor : '');
            b.innerHTML = '<span>' + it.texto + '</span>' + (it.dica ? '<small>' + it.dica + '</small>' : '');
            if (it.desativado || (ocupado && !it.livre)) {
                b.disabled = true;
            } else {
                b.addEventListener('click', function () {
                    fecharMenu();
                    it.acao();
                });
            }
            menu.appendChild(b);
        });

        menu.hidden = false;
        var w = menu.offsetWidth, h = menu.offsetHeight;
        menu.style.left = Math.min(x, window.innerWidth - w - 8) + 'px';
        menu.style.top = Math.min(y, window.innerHeight - h - 8) + 'px';
    }

    function fecharMenu() { menu.hidden = true; }

    function itensDoNo(id) {
        var u = unidades[id];
        if (!u) return [];
        var no = u.no;
        var itens = [];
        var i = -1;
        nos.forEach(function (n, k) { if (n.id === id) i = k; });
        var vCampo = valorDoCampo();
        var rotuloValor = vCampo !== undefined ? vCampo : 'valor aleatório';

        if (ARVORE) {
            itens.push({ titulo: 'Nó ' + no.valor });
            itens.push({ texto: 'Remover ' + no.valor, cor: 'vermelho', acao: function () { executar('arvRemover', { valor: no.valor, semFoco: true }); } });
            itens.push({ texto: 'Buscar ' + no.valor, cor: 'azul', acao: function () { executar('arvBuscar', { valor: no.valor, semFoco: true }); } });
            itens.push({ texto: 'Inserir ' + rotuloValor, cor: 'verde', dica: 'a árvore decide o lugar', acao: function () { executar('arvInserir', { valor: vCampo, semFoco: true }); } });
            itens.push({ texto: 'Editar valor', desativado: true, dica: 'quebraria a ordem da árvore' });
        } else if (TIPO === 'lista_simples' || TIPO === 'lista_dupla') {
            itens.push({ titulo: 'Nó ' + no.valor + ' · posição ' + i });
            itens.push({ texto: 'Inserir antes', dica: rotuloValor + ' na posição ' + i, cor: 'verde', acao: function () { executar('inserirPosicao', { valor: vCampo, pos: i, semFoco: true }); } });
            itens.push({ texto: 'Inserir depois', dica: rotuloValor + ' na posição ' + (i + 1), cor: 'verde', acao: function () { executar('inserirPosicao', { valor: vCampo, pos: i + 1, semFoco: true }); } });
            itens.push({ texto: 'Remover este nó', cor: 'vermelho', acao: function () { executar('removerPosicao', { pos: i, semFoco: true }); } });
            itens.push({ texto: 'Buscar ' + no.valor, cor: 'azul', acao: function () { executar('buscar', { valor: no.valor, semFoco: true }); } });
            itens.push({ editar: criarEditor(no) });
        } else if (TIPO === 'pilha') {
            itens.push({ titulo: 'Nó ' + no.valor + (i === 0 ? ' · topo' : '') });
            if (i === 0) {
                itens.push({ texto: 'Desempilhar (Pop)', cor: 'vermelho', acao: function () { executar('desempilhar'); } });
                itens.push({ texto: 'Ver topo (Peek)', cor: 'azul', acao: function () { executar('espiar'); } });
            } else {
                itens.push({ texto: 'Remover este nó', desativado: true, dica: 'só o topo pode sair (LIFO)' });
            }
        } else {
            itens.push({ titulo: 'Nó ' + no.valor + (i === 0 ? ' · início' : '') + (no.prio ? ' · P' + no.prio : '') });
            if (i === 0) {
                itens.push({ texto: 'Desenfileirar', cor: 'vermelho', acao: function () { executar('desenfileirar'); } });
                itens.push({ texto: 'Ver início', cor: 'azul', acao: function () { executar('frente'); } });
            } else {
                itens.push({ texto: 'Remover este nó', desativado: true, dica: 'só o início pode sair (FIFO)' });
            }
        }

        itens.push('-');
        if (manual[id]) {
            itens.push({ texto: 'Soltar posição', livre: true, dica: 'volta ao lugar automático', acao: function () {
                delete manual[id];
                desenhar();
            } });
        }
        itens.push({ texto: 'Reorganizar todos', livre: true, acao: reorganizar });
        return itens;
    }

    function itensDoFundo() {
        var vCampo = valorDoCampo();
        var rotulo = vCampo !== undefined ? vCampo : 'valor aleatório';
        var primeiraAdd = CONF.ops.filter(function (o) { return OPS[o].grupo === 'add'; })[0];
        var addFim = (TIPO === 'lista_simples' || TIPO === 'lista_dupla') ? 'inserirFim' : primeiraAdd;
        return [
            { titulo: 'Estrutura' },
            { texto: OPS[addFim].rotulo, dica: rotulo, cor: 'verde', acao: function () { executar(addFim, { valor: vCampo, semFoco: true }); } },
            { texto: 'Gerar aleatória', acao: gerarAleatoria },
            { texto: 'Limpar tudo', cor: 'vermelho', acao: limpar },
            '-',
            { texto: 'Ajustar zoom', livre: true, acao: function () { autoEnquadrar = true; enquadrar(true); } },
            { texto: 'Reorganizar posições', livre: true, acao: reorganizar }
        ];
    }

    // editar o dado de um nó (só listas): os ponteiros não mudam
    function criarEditor(no) {
        var f = document.createElement('form');
        f.className = 'sim-menu-editar';
        f.innerHTML = '<label>Editar dado</label><div><input type="number" min="0" max="999" value="' + no.valor + '">' +
                      '<button type="submit">Trocar</button></div>';
        var inp = f.querySelector('input');
        f.addEventListener('submit', function (e) {
            e.preventDefault();
            if (ocupado) return;
            var nv = parseInt(inp.value, 10);
            if (isNaN(nv) || nv < 0 || nv > 999) { inp.classList.add('tremer'); return; }
            var antigo = no.valor;
            no.valor = nv;
            fecharMenu();
            desenhar({ achado: no.id });
            narrar('O dado do nó mudou de ' + antigo + ' para ' + nv + '. Repare que nenhum ponteiro mudou: só o conteúdo do nó.');
            registrar('Editou ' + antigo + ' → ' + nv);
        });
        if (ocupado) inp.disabled = true;
        return f;
    }

    palco.addEventListener('contextmenu', function (e) {
        e.preventDefault();
        var anc = e.target.closest('.sim-ancora');
        var itens = anc ? itensDoNo(+anc.dataset.id) : itensDoFundo();
        if (itens.length) abrirMenu(e.clientX, e.clientY, itens);
    });

    document.addEventListener('pointerdown', function (e) {
        if (!menu.hidden && !e.target.closest('.sim-menu')) fecharMenu();
    });

    /* =============================================================
       13. BOTÕES, ATALHOS E INICIALIZAÇÃO
       ============================================================= */
    CONF.ops.forEach(function (opId) {
        var op = OPS[opId];
        var b = document.createElement('button');
        b.type = 'button';
        b.className = 'sim-btn sim-btn--' + op.grupo;
        b.innerHTML = op.rotulo + ' <small>' + op.custo + '</small>';
        b.addEventListener('click', function () { executar(opId); });
        caixaOps.appendChild(b);
    });

    [campoValor, campoPos].forEach(function (c) {
        if (!c) return;
        c.addEventListener('keydown', function (e) {
            if (e.key !== 'Enter') return;
            e.preventDefault();
            var alvo = (campoPos && c === campoPos) ? 'inserirPosicao' : CONF.ops[ARVORE ? 0 : (TIPO.indexOf('lista') === 0 ? 1 : 0)];
            executar(alvo);
        });
    });

    function limpar() {
        if (ocupado) return;
        nos = [];
        raiz = null;
        manual = {};
        autoEnquadrar = true;
        desenhar();
        narrar('Estrutura limpa.');
        registrar('Limpou a estrutura');
    }

    function gerarAleatoria() {
        if (ocupado) return;
        nos = [];
        raiz = null;
        manual = {};
        autoEnquadrar = true;
        desenhar();

        if (ARVORE) {
            var usados = {}, qtd = 9;
            while (Object.keys(usados).length < qtd) usados[sortear()] = true;
            var vals = Object.keys(usados).map(Number).sort(function () { return Math.random() - 0.5; });
            vals.forEach(inserirSemAnimar);
            desenhar();
            narrar('Árvore gerada inserindo ' + qtd + ' valores em ordem aleatória: ' + vals.join(', ') + '.');
            registrar('Gerou árvore aleatória');
            return;
        }

        for (var i = 0; i < 5; i++) {
            var p = CONF.prioridade ? Math.floor(Math.random() * 5) + 1 : undefined;
            nos.push(criarNo(sortear(), p));
        }
        if (CONF.prioridade) {
            // maior prioridade na frente (sort estável mantém a ordem de chegada)
            nos.sort(function (a, b) { return b.prio - a.prio; });
        }
        desenhar();
        narrar('Estrutura gerada com 5 valores aleatórios.');
        registrar('Gerou estrutura aleatória');
    }

    pegar('sim-limpar').addEventListener('click', limpar);
    pegar('sim-aleatorio').addEventListener('click', gerarAleatoria);

    /* ---- Passo a passo ---- */
    var chkPasso = pegar('sim-passo-a-passo');
    chkPasso.addEventListener('change', function () {
        modoPasso = chkPasso.checked;
        app.classList.toggle('modo-passo', modoPasso);
        if (!modoPasso) avancarPasso(); // libera se estava esperando
        narrar(modoPasso
            ? 'Modo passo a passo ligado: cada etapa espera você clicar em "Próximo passo" (ou apertar espaço).'
            : 'Modo passo a passo desligado: as operações rodam sozinhas.');
    });
    btnProximo.addEventListener('click', avancarPasso);

    /* ---- Painel lateral e tela cheia ---- */
    var btnPainel = pegar('sim-painel-botao');
    btnPainel.addEventListener('click', function () {
        app.classList.toggle('painel-oculto');
        btnPainel.classList.toggle('ativo', app.classList.contains('painel-oculto'));
        setTimeout(function () { if (autoEnquadrar) enquadrar(true); }, 320);
    });

    var btnTela = pegar('sim-tela-cheia');
    function alternarTelaCheia() {
        if (document.fullscreenElement) {
            document.exitFullscreen();
        } else if (app.requestFullscreen) {
            app.requestFullscreen().catch(function () {
                narrar('O navegador não permitiu a tela cheia.', 'erro');
            });
        }
    }
    btnTela.addEventListener('click', alternarTelaCheia);
    document.addEventListener('fullscreenchange', function () {
        var cheia = document.fullscreenElement === app;
        app.classList.toggle('em-tela-cheia', cheia);
        btnTela.textContent = cheia ? 'Sair da tela cheia' : 'Tela cheia';
        setTimeout(function () { if (autoEnquadrar) enquadrar(false); }, 60);
    });

    /* ---- Atalhos de teclado ---- */
    document.addEventListener('keydown', function (e) {
        var digitando = /INPUT|SELECT|TEXTAREA/.test(document.activeElement.tagName);
        if (e.key === 'Escape') fecharMenu();
        if (digitando) return;
        if (e.key === ' ' && modoPasso && liberarPasso) { e.preventDefault(); avancarPasso(); }
        if (e.key === 'f' || e.key === 'F') alternarTelaCheia();
    });

    /* ---- Dica inicial (lembra se foi fechada) ---- */
    var dica = pegar('sim-dica');
    try { if (localStorage.getItem('sim-dica-fechada')) dica.hidden = true; } catch (e) { /* sem storage */ }
    pegar('sim-dica-fechar').addEventListener('click', function () {
        dica.hidden = true;
        try { localStorage.setItem('sim-dica-fechada', '1'); } catch (e) { /* sem storage */ }
    });

    window.addEventListener('resize', function () {
        if (autoEnquadrar) enquadrar(false);
    });

    desenhar();
    enquadrar(false);
    requestAnimationFrame(quadro);
})();
