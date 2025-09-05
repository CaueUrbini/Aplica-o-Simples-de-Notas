<?php
session_start();

// Setando a hora local de Sapo Paulo
date_default_timezone_set('America/Sao_Paulo');

define('NOTES_FILE', 'notes.json');

function getNotes() {
    if (!file_exists(NOTES_FILE)) {
        file_put_contents(NOTES_FILE, json_encode([])); // enconde = Transforma o Php em Json 
    }
    $json = file_get_contents(NOTES_FILE);
    $notes = json_decode($json, true); // decode = json em php 
    if (!is_array($notes)) { // se o array tiver erro ou vazio ele retorna json []
        $notes = [];
    }
    return $notes;
}

function saveNotes(array $notes) {
    $json = json_encode($notes, JSON_PRETTY_PRINT);
    file_put_contents(NOTES_FILE, $json); // salva o arquivo 
}

function addNote($content) {
    $notes = getNotes();
    $newNote = [
        'id' => uniqid(), // id unico com letras e numeros
        'content' => $content,
        'created_at' => date('Y-m-d H:i:s')
    ];
    $notes[] = $newNote; // coloca a nova nota no final do array 
    saveNotes($notes); // todo array é salvo em savenotes
}

function deleteNote($id) {
    $notes = getNotes();
    $notes = array_filter($notes, function($note) use ($id) {
        return $note['id'] !== $id; // retorna todos id que sao diferentes do que procuramos 
    });
    $notes = array_values($notes);
    saveNotes($notes);
}

function editNote($id, $newContent){
    $notes = getNotes();

    foreach ($notes as &$note){ // & foi colocado para editar a nota diretamente no array, sem o & ia só editar sem salvar
        if($note['id'] === $id){
            $note['content'] = $newContent;
            $note['updated_at'] = date('Y-m-d H:i:s');
            break;
        }
    }
    saveNotes($notes);
}

// Foi usado POST para adicionar e editar notas
// Get para deletar 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['edit_id'])) { // se nao for uma edicao, vai adicionar
    $content = isset($_POST['content']) ? trim($_POST['content']) : ''; // tem lugar para colocar o post? se tiver limpe os espacos em branco

    if (empty($content)) {
        $_SESSION['error'] = "O conteúdo da nota não pode estar vazio!";
    } else {
        addNote($content);
        $_SESSION['success'] = "Nota adicionada com sucesso!";
    }
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') { // verifica se o botao é de edicao 
    $editId = $_POST['edit_id']; // para pegar o id da nota
    $editContent = isset($_POST['content']) ? trim($_POST['content']) : '';

    if (empty($editContent)) {
        $_SESSION['error'] = "O conteúdo da nota não pode estar vazio!";
    } else {
        editNote($editId, $editContent);
        $_SESSION['success'] = "Nota editada com sucesso!";
    }
    
    header('Location: index.php');
    exit;
}

if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
    $id = $_GET['id'];
    deleteNote($id);
    $_SESSION['success'] = "Nota excluída com sucesso!";
    header('Location: index.php');
    exit;
}

$notes = getNotes();

$editNote = null;
if (isset($_GET['edit'])) {
    $editId = $_GET['edit'];
    foreach ($notes as $note) {
        if ($note['id'] === $editId) {
            $editNote = $note;
            break;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Aplicação Simples de Notas</title>
</head>
<link rel="stylesheet" href="style.css" />
<body>

<div class="container">
    <!--htmlspecialchars para prevenir ataque xss-->
    <!-- Exibicao das messagens de sucesso e erro -->

    <h1>Aplicação Simples de Notas</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="message success" id="msg-success"><?=htmlspecialchars($_SESSION['success'])?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="message error" id="msg-error"><?=htmlspecialchars($_SESSION['error'])?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <!-- Post de salve e edit -->
    <form method="POST" action="index.php">
        <label for="content"><?= $editNote ? 'Editar Nota:' : 'Nova Nota:' ?></label>
        <textarea name="content" id="content" placeholder="Digite sua nota aqui..." required><?= $editNote ? htmlspecialchars($editNote['content']) : '' ?></textarea> <!-- coloquei o required para poupar processamento--> 
        <div class="form-buttons">
            <?php if ($editNote): ?>
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="edit_id" value="<?= htmlspecialchars($editNote['id']) ?>" />
                <button type="submit">Salvar Alteração</button>
                <a href="index.php" class="cancel-link" title="Cancelar edição">Cancelar</a>
            <?php else: ?>
                <button type="submit">Adicionar Nota</button>
            <?php endif; ?>
        </div>
    </form>

    <?php if (count($notes) === 0): ?>
        <p style="text-align:center; color:#666;">Nenhuma nota cadastrada.</p>
    <?php else: ?>
        <ul class="notes-list">
            <?php foreach ($notes as $note): ?>
                <li>
                    <div class="content"><?=htmlspecialchars($note['content'])?></div>
                    <div class="created_at">Criado em: <?=htmlspecialchars($note['created_at'])?></div>
                    <?php if (!empty($note['updated_at'])): ?>
                        <div class="updated_at">Atualizado em: <?=htmlspecialchars($note['updated_at'])?></div>
                    <?php endif; ?>
                    <div class="actions">
                        <a href="#" class="delete-link" data-id="<?=htmlspecialchars($note['id'])?>" title="Excluir Nota">Excluir</a>
                        <a href="index.php?edit=<?=urlencode($note['id'])?>" class="edit-link" title="Editar Nota">Editar</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

</div>
<!-- Modal para confirmar a exclusao da nota--> 
<div class="modal-overlay" id="modal-overlay">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <p id="modal-title">Tem certeza que deseja excluir esta nota?</p>
        <div class="modal-buttons">
            <button class="confirm-btn" id="confirm-delete-btn">Sim, excluir</button>
            <button class="cancel-btn" id="cancel-delete-btn">Cancelar</button>
        </div>
    </div>
</div>

<!-- confirmação via Js antes de excluir uma nota e fade out das mensagens -->
<script>
    window.addEventListener('DOMContentLoaded', () => {
        const successMsg = document.getElementById('msg-success');
        const errorMsg = document.getElementById('msg-error');
        [successMsg, errorMsg].forEach(msg => {
            if(msg){
                setTimeout(() => {
                    msg.style.opacity = '0';
                    setTimeout(() => msg.remove(), 500);
                }, 5000);
            }
        });
    });

    const modalOverlay = document.getElementById('modal-overlay');
    const confirmBtn = document.getElementById('confirm-delete-btn');
    const cancelBtn = document.getElementById('cancel-delete-btn');
    let deleteId = null;

    document.querySelectorAll('.delete-link').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            deleteId = link.getAttribute('data-id');
            modalOverlay.style.display = 'flex';
        });
    });

    confirmBtn.addEventListener('click', () => {
        if(deleteId){
            window.location.href = `index.php?action=delete&id=${encodeURIComponent(deleteId)}`;
        }
    });

    cancelBtn.addEventListener('click', () => {
        modalOverlay.style.display = 'none';
        deleteId = null;
    });
</script>

</body>
</html>