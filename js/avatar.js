/* =============================================================
   avatar.js — Provador do avatar
   Caminho: js/avatar.js

   Passar o mouse (ou focar com o teclado) em um card com
   data-provar="chave" e data-slot="slot" veste o item no avatar
   que tem o atributo data-provador. Ao sair, volta ao que estava.
   No celular, o botão "Provar" do card liga/desliga a prova.
   ============================================================= */

(function () {
    'use strict';

    var caixa = document.querySelector('[data-provador]');
    if (!caixa) return;

    var avatar = caixa.querySelector('svg[data-avatar]');
    var aviso = caixa.querySelector('[data-provador-aviso]');
    if (!avatar) return;

    var provandoAgora = null; // card que está sendo provado

    function camadasDoSlot(slot) {
        return avatar.querySelectorAll('.av-camada[data-slot="' + slot + '"]');
    }

    function provar(card) {
        if (provandoAgora && provandoAgora !== card) restaurar(provandoAgora);
        var slot = card.dataset.slot, chave = card.dataset.provar;

        camadasDoSlot(slot).forEach(function (g) {
            var mostrar = g.dataset.item === chave;
            g.classList.toggle('av-oculta', !mostrar);
            g.classList.remove('av-provando');
            if (mostrar && g.dataset.visivel !== '1') {
                void g.getBBox(); // reinicia a animação de "prova"
                g.classList.add('av-provando');
            }
        });

        provandoAgora = card;
        card.classList.add('provando');
        caixa.classList.add('esta-provando');
        if (aviso) aviso.textContent = card.dataset.nome ? 'Provando: ' + card.dataset.nome : 'Provando…';
    }

    function restaurar(card) {
        camadasDoSlot(card.dataset.slot).forEach(function (g) {
            g.classList.toggle('av-oculta', g.dataset.visivel !== '1');
            g.classList.remove('av-provando');
        });
        card.classList.remove('provando');
        if (provandoAgora === card) {
            provandoAgora = null;
            caixa.classList.remove('esta-provando');
            if (aviso) aviso.textContent = '';
        }
    }

    document.querySelectorAll('[data-provar]').forEach(function (card) {
        // só mouse de verdade (no celular o toque usa o botão "Provar")
        card.addEventListener('pointerenter', function (e) { if (e.pointerType === 'mouse') provar(card); });
        card.addEventListener('pointerleave', function (e) { if (e.pointerType === 'mouse') restaurar(card); });

        // teclado: focar um botão do card também prova o item
        card.addEventListener('focusin', function (e) {
            if (e.target.matches(':focus-visible') && !e.target.matches('[data-botao-provar]')) provar(card);
        });
        card.addEventListener('focusout', function (e) {
            if (!card.contains(e.relatedTarget) && provandoAgora === card) restaurar(card);
        });

        // botão "Provar" (útil no celular, onde não existe "passar o mouse")
        var botao = card.querySelector('[data-botao-provar]');
        if (botao) {
            botao.addEventListener('click', function () {
                if (provandoAgora === card) restaurar(card);
                else provar(card);
                if (window.matchMedia('(max-width: 900px)').matches) {
                    caixa.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        }
    });
})();
