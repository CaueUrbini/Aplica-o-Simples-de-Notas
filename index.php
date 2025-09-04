<?php
session_start();

// Hour in Brazil - São Paulo 
date_default_timezone_set('America/Sao_Paulo');

define('NOTES_FILE', 'notes.json');

function getNotes() {
    if (!file_exists(NOTES_FILE)) {
        file_put_contents(NOTES_FILE, json_encode([])); // encode = Php => Json
    }
    $json = file_get_contents(NOTES_FILE);
    $notes = json_decode($json, true);
    if (!is_array($notes)) {  // If the array empty or has any errors returns Json[]
        $notes = [];
    }
    return $notes;
}

function saveNotes(array $notes) {
    $json = json_encode($notes, JSON_PRETTY_PRINT); //  Better framing in json 
    file_put_contents(NOTES_FILE, $json);   // Saves all "content" added in Notes  
}

function addNote($content) {
    $notes = getNotes();
    $newNote = [
        'id' => uniqid(),
        'content' => $content,
        'created_at' => date('Y-m-d H:i:s')
    ];
    $notes[] = $newNote; // Add at least in array
    saveNotes($notes); // Add all array 
}

function deleteNote($id) {
    $notes = getNotes();  // Pull all notes
    $notes = array_filter($notes, function($note) use ($id) {  // with "&" edit the original but without just make a copy - dont save in array 
        return $note['id'] !== $id; // keep all notes except the one with the id matching
    });
    $notes = array_values($notes); 
    saveNotes($notes);
}

function editNote($id, $newContent){
    $notes = getNotes();

    foreach ($notes as &$note){
        if($note['id'] === $id){
            $note['content'] = $newContent;
            $note['updated_at'] = date('Y-m-d H:i:s');
            break;
        }
    }
    saveNotes($notes);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['edit_id'])) {
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';

    if (empty($content)) {
        $_SESSION['error'] = "O conteúdo da nota não pode estar vazio!";
    } else {
        addNote($content);
        $_SESSION['success'] = "Nota adicionada com sucesso!";
    }
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $editId = $_POST['edit_id'];
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

    <h1>Aplicação Simples de Notas</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="message success" id="msg-success"><?=htmlspecialchars($_SESSION['success'])?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="message error" id="msg-error"><?=htmlspecialchars($_SESSION['error'])?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="POST" action="index.php">
        <label for="content"><?= $editNote ? 'Editar Nota:' : 'Nova Nota:' ?></label>
        <textarea name="content" id="content" placeholder="Digite sua nota aqui..." required><?= $editNote ? htmlspecialchars($editNote['content']) : '' ?></textarea>
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

<div class="modal-overlay" id="modal-overlay">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <p id="modal-title">Tem certeza que deseja excluir esta nota?</p>
        <div class="modal-buttons">
            <button class="confirm-btn" id="confirm-delete-btn">Sim, excluir</button>
            <button class="cancel-btn" id="cancel-delete-btn">Cancelar</button>
        </div>
    </div>
</div>