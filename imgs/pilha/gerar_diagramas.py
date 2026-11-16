"""Gera os diagramas da pagina de Pilha Encadeada (imgs/pilha/*.png)."""

import matplotlib
matplotlib.use("Agg")

import matplotlib.pyplot as plt
from matplotlib.patches import FancyArrow, FancyBboxPatch

AZUL = "#0ea5e9"
ESCURO = "#0f172a"
CINZA = "#64748b"
FUNDO_NO = "#e0f2fe"

plt.rcParams["font.family"] = "DejaVu Sans"


def caixa_no(ax, x, y, largura, altura, dado, rotulo_dado="Dado", rotulo_prox="Próximo"):
    """Desenha um no com dois campos: dado e ponteiro para o proximo."""
    ax.add_patch(
        FancyBboxPatch(
            (x, y), largura, altura,
            boxstyle="round,pad=0.02,rounding_size=0.12",
            linewidth=2, edgecolor=ESCURO, facecolor=FUNDO_NO,
        )
    )
    meio = x + largura * 0.6
    ax.plot([meio, meio], [y, y + altura], color=ESCURO, linewidth=2)
    ax.text(x + largura * 0.3, y + altura * 0.62, str(dado),
            ha="center", va="center", fontsize=17, fontweight="bold", color=ESCURO)
    ax.text(x + largura * 0.3, y + altura * 0.22, rotulo_dado,
            ha="center", va="center", fontsize=9, color=CINZA)
    ax.text(meio + (x + largura - meio) / 2, y + altura * 0.5, rotulo_prox,
            ha="center", va="center", fontsize=9, color=CINZA)
    return meio + (x + largura - meio) / 2


def seta(ax, x1, y1, x2, y2, cor=AZUL):
    ax.add_patch(
        FancyArrow(x1, y1, x2 - x1, y2 - y1,
                   width=0.012, head_width=0.09, head_length=0.12,
                   length_includes_head=True, color=cor)
    )


def nova_figura(largura, altura, titulo):
    fig, ax = plt.subplots(figsize=(largura, altura))
    ax.set_axis_off()
    fig.patch.set_facecolor("white")
    ax.set_title(titulo, fontsize=15, fontweight="bold", color=ESCURO, pad=14)
    return fig, ax


def figura_topo_ponteiros(caminho):
    """Figura 1 - topo da pilha e encadeamento dos nos ate NULL."""
    fig, ax = nova_figura(9, 4.2, "Pilha Encadeada: o Topo aponta para o último elemento inserido")
    ax.set_xlim(0, 9)
    ax.set_ylim(0, 4.2)

    ax.text(0.9, 3.0, "Topo", ha="center", va="center", fontsize=15,
            fontweight="bold", color=AZUL)
    ax.text(0.9, 2.62, "(referência)", ha="center", va="center", fontsize=9, color=CINZA)

    x = 1.8
    valores = [30, 20, 10]
    centros = []
    for valor in valores:
        centros.append((x, caixa_no(ax, x, 2.2, 1.9, 1.1, valor)))
        x += 2.3

    seta(ax, 1.35, 2.85, 1.78, 2.78)
    for i in range(len(valores) - 1):
        origem = centros[i][1]
        destino = centros[i + 1][0]
        seta(ax, origem, 2.5, destino - 0.03, 2.5)
    seta(ax, centros[-1][1], 2.5, centros[-1][0] + 2.25, 2.5)
    ax.text(centros[-1][0] + 2.55, 2.5, "NULL", ha="left", va="center",
            fontsize=13, fontweight="bold", color=ESCURO)

    ax.text(2.75, 1.85, "topo (último empilhado)", ha="center", fontsize=10, color=CINZA)
    ax.text(7.35, 1.85, "base (primeiro empilhado)", ha="center", fontsize=10, color=CINZA)
    ax.text(4.5, 1.05,
            "Push insere sempre antes do Topo; Pop remove sempre o nó apontado pelo Topo.",
            ha="center", fontsize=11, color=ESCURO)
    ax.text(4.5, 0.6,
            "Nenhum outro nó pode ser acessado diretamente — daí o princípio LIFO.",
            ha="center", fontsize=11, color=CINZA)

    fig.tight_layout()
    fig.savefig(caminho, dpi=150, facecolor="white")
    plt.close(fig)


def pilha_vertical(ax, x, valores, titulo, destaque=None, legenda=""):
    """Desenha uma pilha na vertical com o topo em cima."""
    ax.text(x + 0.6, 3.45, titulo, ha="center", fontsize=12,
            fontweight="bold", color=ESCURO)
    altura = 0.55
    y = 2.6
    if not valores:
        ax.text(x + 0.6, 2.87, "vazia", ha="center", va="center",
                fontsize=12, color=CINZA, style="italic")
    for indice, valor in enumerate(valores):
        cor = "#bae6fd" if (destaque is not None and indice == 0) else FUNDO_NO
        ax.add_patch(
            FancyBboxPatch(
                (x, y), 1.2, altura,
                boxstyle="round,pad=0.01,rounding_size=0.08",
                linewidth=2, edgecolor=ESCURO, facecolor=cor,
            )
        )
        ax.text(x + 0.6, y + altura / 2, str(valor), ha="center", va="center",
                fontsize=13, fontweight="bold", color=ESCURO)
        if indice == 0:
            ax.text(x + 1.30, y + altura / 2, "\u2190 Topo", ha="left", va="center",
                    fontsize=10, color=AZUL, fontweight="bold")
        y -= altura + 0.08
    if legenda:
        ax.text(x + 0.6, 0.35, legenda, ha="center", fontsize=10, color=CINZA)


def figura_push_pop(caminho):
    """Figura 2 - sequencia de Push e Pop passo a passo."""
    fig, ax = nova_figura(13, 4.6, "Sequência de operações: Push(10), Push(20), Push(30) e Pop()")
    ax.set_xlim(0, 13)
    ax.set_ylim(0, 3.9)

    estados = [
        ([], "pilha vazia\nIsEmpty() = true"),
        ([10], "Push(10)"),
        ([20, 10], "Push(20)"),
        ([30, 20, 10], "Push(30)"),
        ([20, 10], "Pop() \u2192 30"),
    ]
    x = 0.5
    passo = 2.5
    for indice, (valores, legenda) in enumerate(estados):
        pilha_vertical(ax, x, valores, f"Passo {indice + 1}", destaque=True, legenda=legenda)
        if indice < len(estados) - 1:
            seta(ax, x + 2.05, 1.0, x + 2.40, 1.0, cor=CINZA)
        x += passo

    fig.tight_layout()
    fig.savefig(caminho, dpi=150, facecolor="white")
    plt.close(fig)


def figura_estrutura_no(caminho):
    """Figura 3 - campos de um no da pilha encadeada."""
    fig, ax = nova_figura(7, 2.8, "Estrutura do nó: dado + referência para o próximo")
    ax.set_xlim(0, 7)
    ax.set_ylim(0, 2.6)

    caixa_no(ax, 1.1, 1.1, 2.6, 1.1, 42)
    seta(ax, 3.72, 1.65, 4.6, 1.65)
    ax.text(4.75, 1.65, "próximo nó\n(ou NULL na base)", ha="left", va="center",
            fontsize=11, color=ESCURO)
    ax.text(3.5, 0.5,
            "O nó não conhece quem aponta para ele: o acesso começa sempre pelo Topo.",
            ha="center", fontsize=10, color=CINZA)

    fig.tight_layout()
    fig.savefig(caminho, dpi=150, facecolor="white")
    plt.close(fig)


if __name__ == "__main__":
    import os

    destino = os.path.dirname(os.path.abspath(__file__))
    figura_topo_ponteiros(os.path.join(destino, "figura1.png"))
    figura_push_pop(os.path.join(destino, "figura2.png"))
    figura_estrutura_no(os.path.join(destino, "figura3.png"))
    print("diagramas gerados em", destino)
