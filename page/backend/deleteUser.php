<?php
require '../../_base.php';
// ----------------------------------------------------------------------------

if (is_post()) {
    $id = req('id');

    $stm = $_db->prepare('UPDATE users 
    SET is_delete = "1" 
    WHERE user_id = ?');
    $stm->execute([$id]);

    temp('info', 'Record deleted');
    redirect('/page/backend/admin.php');
}

