# Desafio: Aplicação Simples de Notas (CRUD Básico com Arquivos JSON)


## 📝 Descrição
Desenvolver uma aplicação PHP web para criar, listar e excluir notas simples. As notas serão armazenadas em um arquivo JSON.

Conceitos Chave a Serem Praticados:

Manipulação de Formulários ($_POST, $_GET)
Validação de Dados de Entrada
Manipulação de Arrays (adição, remoção, busca)
Leitura e Escrita de Arquivos (especificamente JSON)
Funções para organizar o código
Estruturas de Controle (if/else, foreach)
Sessões ($_SESSION) para mensagens de feedback
PRG Pattern (Post/Redirect/Get) para evitar reenvio de formulários
Cenário: Você precisa criar uma ferramenta básica para um usuário registrar e gerenciar notas rápidas. Cada nota terá um ID único, o conteúdo da nota e a data/hora de criação.

Requisitos Detalhados:

1. Estrutura do Projeto:

Crie um único arquivo PHP principal, por exemplo, index.php.
Crie um arquivo para armazenar as notas, por exemplo, notes.json. Inicialmente, este arquivo pode estar vazio ou conter um array JSON vazio [].
2. Formato de Dados (notes.json):

O arquivo notes.json deve armazenar um array de objetos JSON, onde cada objeto representa uma nota.

Cada nota deve ter as seguintes propriedades:

id: (string/inteiro) Um identificador único para a nota.
content: (string) O texto da nota.
created_at: (string) A data e hora em que a nota foi criada (ex: date('Y-m-d H:i:s')).
Exemplo do conteúdo de notes.json com duas notas:

[
    {
        "id": "1678886400",
        "content": "Lembrete: Comprar leite e pão.",
        "created_at": "2024-03-15 10:00:00"
    },
    {
        "id": "1678886460",
        "content": "Idéia para o projeto X: Usar API Y.",
        "created_at": "2024-03-15 10:01:00"
    }
]
3. Funcionalidades da Aplicação:

*   **3.1. Formulário para Adicionar Nova Nota:**
    *   No `index.php`, crie um formulário HTML com um `<textarea>` para o conteúdo da nota e um botão de submit.
    *   O formulário deve ser enviado via método `POST`.
    *   **Validação:** Ao receber os dados do formulário, verifique se o campo de conteúdo da nota não está vazio. Se estiver, exiba uma mensagem de erro.
    *   **Geração de ID:** Crie um ID único para cada nova nota (você pode usar `uniqid()` ou `time()` como um ID simples).
    *   **Salvamento:** Adicione a nova nota (com `id`, `content` e `created_at`) ao array de notas existente lido do `notes.json`. Codifique o array atualizado de volta para JSON e salve no arquivo `notes.json`.
    *   **Redirecionamento (PRG Pattern):** Após adicionar uma nota com sucesso, redirecione o usuário de volta para a mesma página `index.php` para limpar os dados do POST e evitar que a nota seja adicionada novamente se a página for recarregada.

*   **3.2. Listagem de Notas:**
    *   Leia o conteúdo do arquivo `notes.json`.
    *   Decodifique o JSON para um array PHP.
    *   Exiba cada nota em uma lista HTML (por exemplo, `<ul>` ou `<div>`s).
    *   Para cada nota, mostre o `content` e o `created_at`.
    *   **Ação de Excluir:** Ao lado de cada nota listada, adicione um link ou botão "Excluir". Este link deve enviar o `id` da nota a ser excluída via método `GET` (ex: `index.php?action=delete&id=123`).

*   **3.3. Excluir Nota:**
    *   Quando um link "Excluir" for clicado e o `id` da nota for recebido via `GET`:
    *   Leia o array de notas do `notes.json`.
    *   Remova a nota correspondente ao `id` recebido do array. Você pode usar `array_filter()` ou iterar e recriar o array.
    *   Codifique o array atualizado de volta para JSON e salve no arquivo `notes.json`.
    *   Redirecione o usuário de volta para `index.php` após a exclusão.

*   **3.4. Mensagens de Feedback (usando Sessões):**
    *   Implemente um sistema para exibir mensagens de sucesso (ex: "Nota adicionada com sucesso!", "Nota excluída!") ou de erro (ex: "O conteúdo da nota não pode estar vazio!").
    *   Use a superglobal `$_SESSION` para armazenar essas mensagens, exibindo-as uma vez e depois limpando-as. Lembre-se de iniciar a sessão com `session_start()` no topo do `index.php`.
Dicas para o Desenvolvimento:

Manipulação de Arquivos: Use file_get_contents('notes.json') para ler e file_put_contents('notes.json', $json_data) para escrever.
JSON: Use json_decode($json_string, true) para converter JSON em array PHP (o true é crucial para obter um array associativo) e json_encode($php_array, JSON_PRETTY_PRINT) para converter array PHP em JSON formatado (o JSON_PRETTY_PRINT é opcional, mas ajuda na legibilidade do arquivo).
Validação: Sempre verifique se os dados do formulário ($_POST ou $_GET) existem e são do tipo esperado antes de usá-los (com isset() e !empty()).
Segurança Básica: Ao exibir o conteúdo da nota, use htmlspecialchars() para evitar ataques de XSS (Cross-Site Scripting), garantindo que caracteres HTML no conteúdo da nota sejam exibidos como texto e não interpretados como código.
Organização: Você pode criar funções separadas para tarefas como getNotes(), saveNotes($notes), addNote($content), deleteNote($id). Isso deixará seu código mais limpo e modular.
Pontos Extra (Desafio Opcional para Próximos Passos):

Funcionalidade de Edição: Adicione um link/botão "Editar" ao lado de cada nota. Ao clicar, o formulário de adição se transforma em um formulário de edição, preenchido com o conteúdo da nota selecionada.
CSS Básico: Adicione um pouco de CSS para tornar a interface mais agradável (mesmo que seja em um <style> no próprio index.php ou em um arquivo .css separado).
Confirmação de Exclusão: Adicione uma confirmação via JavaScript antes de excluir uma nota.

## 🚀 Tecnologias utilizadas
- **PHP**
- **JSON**
- **HTML**
- **CSS**
