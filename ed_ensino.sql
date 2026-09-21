DROP DATABASE IF EXISTS ed_ensino;
CREATE DATABASE ed_ensino CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ed_ensino;

CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  cpf VARCHAR(14) NOT NULL,
  senha VARCHAR(255) NOT NULL,
  foto VARCHAR(255) NOT NULL DEFAULT 'default.png',
  moedas INT NOT NULL DEFAULT 0,
  data_cadastro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uk_usuarios_email (email),
  UNIQUE KEY uk_usuarios_cpf (cpf)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE recuperacao_senha (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  token_hash CHAR(64) NOT NULL,
  expira_em DATETIME NOT NULL,
  usado_em DATETIME NULL,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_token_hash (token_hash),
  CONSTRAINT fk_rec_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE quiz_partidas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  acertos INT NOT NULL DEFAULT 0,
  total_perguntas INT NOT NULL DEFAULT 0,
  moedas_ganhas INT NOT NULL DEFAULT 0,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_quiz_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE itens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  categoria VARCHAR(40) NOT NULL,
  raridade VARCHAR(40) NOT NULL DEFAULT 'comum',
  preco INT NOT NULL,
  descricao VARCHAR(255) NOT NULL,
  habilidade VARCHAR(80) NOT NULL,
  imagem VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE inventario (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  item_id INT NOT NULL,
  equipado TINYINT(1) NOT NULL DEFAULT 0,
  comprado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uk_inventario_usuario_item (usuario_id, item_id),
  CONSTRAINT fk_inventario_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  CONSTRAINT fk_inventario_item FOREIGN KEY (item_id) REFERENCES itens(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE moedas_movimentacoes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  valor INT NOT NULL,
  tipo VARCHAR(20) NOT NULL,
  motivo VARCHAR(255) NOT NULL,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_moedas_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO itens (nome, categoria, raridade, preco, descricao, habilidade, imagem) VALUES
('Óculos de Foco', 'rosto', 'comum', 20, 'Dá foco para analisar alternativas difíceis.', 'Dica na pergunta', 'imgs/avatar/oculos_foco.svg'),
('Cabelo Relâmpago', 'cabelo', 'comum', 25, 'Visual rápido para quem responde sem travar.', 'Animação extra', 'imgs/avatar/cabelo_relampago.svg'),
('Chapéu da Lógica', 'chapeu', 'raro', 40, 'Ajuda a pensar em ponteiros e invariantes.', 'Eliminar alternativa', 'imgs/avatar/chapeu_logica.svg'),
('Roupa do Tempo', 'roupa', 'raro', 45, 'Uma roupa para partidas longas de estruturas.', 'Tempo extra', 'imgs/avatar/roupa_tempo.svg'),
('Amuleto das Moedas', 'acessorio', 'epico', 60, 'Aumenta a recompensa de partidas perfeitas.', 'Bônus de moedas', 'imgs/avatar/amuleto_moedas.svg'),
('Máscara Binária', 'rosto', 'raro', 55, 'Mostra padrões escondidos em questões de código.', 'Revelar pista', 'imgs/avatar/mascara_binaria.svg'),
('Capa da Recursão', 'roupa', 'epico', 80, 'Boa para voltar uma etapa e tentar de novo.', 'Voltar uma pergunta', 'imgs/avatar/capa_recursao.svg'),
('Luvas de Ponteiro', 'acessorio', 'raro', 50, 'Ajuda a seguir referências entre nós encadeados.', 'Destacar ponteiros', 'imgs/avatar/luvas_ponteiro.svg'),
('Botas de Busca', 'acessorio', 'comum', 35, 'Feitas para percorrer listas sem se perder.', 'Pular pergunta', 'imgs/avatar/botas_busca.svg'),
('Coroa TAD', 'chapeu', 'epico', 90, 'Um item raro para dominar conceitos abstratos.', 'Bônus perfeito', 'imgs/avatar/coroa_tad.svg');
