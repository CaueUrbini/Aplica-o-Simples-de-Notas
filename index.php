<?php
session_start();

// Set hour in Brazil
date_default_timezone_set('America/Sao_Paulo');


define('Notes_file', 'notes.json'); // Connect json with the file

function getNotes()
{
    if (!file_exists(Notes_file)) {
        file_put_contents(Notes_file, json_encode([])); // encode = Php => Json
    }
    $json = file_get_contents(Notes_file);
    $notes = json_decode($json, true); //  Json => Php  read 
    if (!is_array($notes)) {  // If the array empty or has any errors returns Json[]
        $notes = [];
    }
    return $notes;
}

function saveNotes(array $notes)
{
    $json = json_encode($notes, JSON_PRETTY_PRINT); //  Better framing in json 
    file_put_contents(Notes_file, $json);     // Saves all "content" added in Notes  
}

function addNote($content)
{
    $notes = getNotes();
    $newNote = [
        'id' => uniqid(),
        'content' => $content,
        'created_at' => date('d-m-Y H:i:s')
    ];
    $notes[] = $newNote; // Adds at the end of the array
    saveNotes($notes); // Save all array 
}

function editNote($id, $newContent)
{
    $notes = getNotes(); // Pull all notes
    foreach ($notes as &$note) {  // with "&" edit the original but without just make a copy - dont save in array 
        if ($note['id'] === $id) {  // this id is the looking for? 
            $note['content'] = $newContent;
            $note['update_at'] = date('d-m-Y H:i:s');
            break; // Foreach does not stop automatically
        }
    }
    saveNotes($notes);
}

function deleteNote($id)
{
    $notes = getNotes();
    $notes = array_filter($notes, function ($note) use ($id) {
        return $note['id'] !== $id;  // ids != go    ids == trash 
    });
}
