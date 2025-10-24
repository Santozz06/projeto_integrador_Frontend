
CREATE DATABASE IF NOT EXISTS escola_db;
USE escola_db;

-- Tabela de Usuários
CREATE TABLE Usuarios (
    ID_Usuario INT AUTO_INCREMENT PRIMARY KEY,
    Login VARCHAR(50) UNIQUE NOT NULL,
    Senha VARCHAR(255) NOT NULL,
    Nome_Completo VARCHAR(100) NOT NULL,
    Data_Nascimento DATE,
    Sexo VARCHAR(20),
    CPF VARCHAR(14) UNIQUE,
    RG VARCHAR(30),
    Orgao_Exp VARCHAR(10),
    UF_Exp VARCHAR(2),
    Raca_Etnia VARCHAR(50),
    Estado_Civil VARCHAR(30),
    Nacionalidade VARCHAR(100),
    Naturalidade VARCHAR(150),
    Filiacao VARCHAR(255),
    CEP VARCHAR(12),
    Logradouro VARCHAR(150),
    Numero VARCHAR(20),
    Complemento VARCHAR(100),
    Bairro VARCHAR(100),
    UF_Endereco INT,
    Municipio_Endereco INT,
    Endereco VARCHAR(200),
    Telefone VARCHAR(20),
    Telefone_Fixo VARCHAR(20),
    Celular VARCHAR(20),
    Email VARCHAR(100) UNIQUE,
    Data_Expedicao DATE,
    Possui_Necessidades_Especiais BOOLEAN DEFAULT FALSE,
    IsAdmin BOOLEAN DEFAULT FALSE,
    Ativo TINYINT(1) NOT NULL DEFAULT 1
);

-- Tabela de Alunos
CREATE TABLE Alunos (
    ID_Aluno INT PRIMARY KEY,
    FOREIGN KEY (ID_Aluno) REFERENCES Usuarios(ID_Usuario) ON DELETE CASCADE
);

-- Tabela de Professores
CREATE TABLE Professores (
    ID_Professor INT PRIMARY KEY,
    Formacao VARCHAR(100),
    Data_Ingresso DATE,
    Area_Atuacao VARCHAR(255),
    Matricula VARCHAR(50) UNIQUE,
    FOREIGN KEY (ID_Professor) REFERENCES Usuarios(ID_Usuario) ON DELETE CASCADE
);

-- Tabela de Turmas
CREATE TABLE Turmas (
    ID_Turma INT AUTO_INCREMENT PRIMARY KEY,
    Nome_Turma VARCHAR(50) NOT NULL,
    Etapa VARCHAR(50),
    Ano_Letivo INT NOT NULL,
    Turno VARCHAR(20),
    Capacidade_Alunos INT,
    Sala VARCHAR(20)
);

-- Tabela de Disciplinas
CREATE TABLE Disciplinas (
    ID_Disciplina INT AUTO_INCREMENT PRIMARY KEY,
    Nome_Disciplina VARCHAR(100) NOT NULL,
    Carga_Horaria INT,
    Ano_Letivo INT,
    Etapa VARCHAR(50),
    ID_Professor INT,
    FOREIGN KEY (ID_Professor) REFERENCES Professores(ID_Professor)
);

-- Tabela de Matriculas
CREATE TABLE Matriculas (
    ID_Matricula INT AUTO_INCREMENT PRIMARY KEY,
    ID_Aluno INT NOT NULL,
    ID_Turma INT NOT NULL,
    Data_Matricula DATE NOT NULL,
    Status VARCHAR(20) DEFAULT 'Ativa',
    Data_Saida DATE,
    Tipo_Matricula VARCHAR(20),
    Ano_Letivo INT,
    FOREIGN KEY (ID_Aluno) REFERENCES Alunos(ID_Aluno),
    FOREIGN KEY (ID_Turma) REFERENCES Turmas(ID_Turma),
    UNIQUE KEY (ID_Aluno, ID_Turma, Ano_Letivo)
);

-- Tabela de Notas
CREATE TABLE Notas (
    ID_Nota INT AUTO_INCREMENT PRIMARY KEY,
    ID_Matricula INT NOT NULL,
    ID_Disciplina INT NOT NULL,
    Etapa VARCHAR(50),
    Nota DECIMAL(4,2),
    Observacoes TEXT,
    FOREIGN KEY (ID_Matricula) REFERENCES Matriculas(ID_Matricula),
    FOREIGN KEY (ID_Disciplina) REFERENCES Disciplinas(ID_Disciplina)
);

-- Tabela de Frequencia
CREATE TABLE Frequencias (
    ID_Frequencia INT AUTO_INCREMENT PRIMARY KEY,
    ID_Matricula INT NOT NULL,
    Data DATE NOT NULL,
    Presenca BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (ID_Matricula) REFERENCES Matriculas(ID_Matricula)
);

-- Tabela de Planos de Ensino
CREATE TABLE Planos_Ensino (
    ID_Plano INT AUTO_INCREMENT PRIMARY KEY,
    ID_Disciplina INT NOT NULL,
    Conteudo TEXT,
    Objetivos TEXT,
    Metodologia TEXT,
    Avaliacao TEXT,
    FOREIGN KEY (ID_Disciplina) REFERENCES Disciplinas(ID_Disciplina)
);

-- Tabela de Calendário Acadêmico
CREATE TABLE Calendario_Academico (
    ID_Evento INT AUTO_INCREMENT PRIMARY KEY,
    Nome_Evento VARCHAR(100) NOT NULL,
    Descricao TEXT,
    Data_Inicio DATE NOT NULL,
    Data_Fim DATE,
    Tipo_Evento VARCHAR(50),
    Ano_Letivo INT
);

-- Tabela de Acompanhamento para Necessidades Especiais
CREATE TABLE Acompanhamento_NEE (
    ID_Acompanhamento INT AUTO_INCREMENT PRIMARY KEY,
    ID_Aluno INT NOT NULL,
    Tipo VARCHAR(50) NOT NULL,
    Descricao TEXT,
    Acompanhamento_Especializado TEXT,
    FOREIGN KEY (ID_Aluno) REFERENCES Alunos(ID_Aluno)
);

-- Tabela de Professores por Turma
CREATE TABLE Professores_Turmas (
    ID_Professor INT,
    ID_Turma INT,
    PRIMARY KEY (ID_Professor, ID_Turma),
    FOREIGN KEY (ID_Professor) REFERENCES Professores(ID_Professor),
    FOREIGN KEY (ID_Turma) REFERENCES Turmas(ID_Turma)
);

-- iserindo um admin padrão
INSERT INTO Usuarios (Login, Senha, Nome_Completo, Data_Nascimento, Sexo, CPF, Email, IsAdmin) VALUES
('admin', '1234', 'Administrador do Sistema', '1980-01-01', 'M', '12345678901', 'admin@escola.com', TRUE);