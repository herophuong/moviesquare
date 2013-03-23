<?php
require 'header.php';
if (!(isset($_REQUEST['mode']))) {
	$error = "You are not allow to directly access this page";
} else {
	if ($_REQUEST['mode'] == 'edit') {
		$action = '';
		$inputs = array();
		$type = $_POST['type'];
		switch ($_POST['type']) {
			case 'movie':
				$movie = $_POST['movie'];
				// Preprocess
				if (!$movie['id'])
					unset($movie['id']);
				if (isset($movie['featured']) && $movie['featured'])
					$movie['class'] .= ' movie_featured';
				unset($movie['featured']);
				
				// Store
				$updated_id = $db->storeArray($movie, 'Movies');
				if (!$updated_id) {
					$action = 'movies-edit.php';
					$inputs = $_POST['movie'];
					$input_error = $db->getError();
				} else {
					// Redirect
					header('Location: movies-edit.php?id='.$updated_id);
					return;
				}
				
				break;
			default:
				$error = "Non-supported type of data for processing";
				break;
		}
	} elseif ($_REQUEST['mode'] == 'delete') {
		switch ($_REQUEST['type']) {
			case 'movie':
				$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
				$query = "DELETE FROM `Movies` WHERE id = ".(int) $id;
				$db->query($query);
				break;
			default:
				$error = "Non-supported type of data for processing";
				break;
		}
		if (!empty($_SERVER['HTTP_REFERER']))
			header('Location: '.$_SERVER['HTTP_REFERER']);
	}
}

?>
<div class="content">
	<?php if (empty($error)) { ?>
	
	<form id="form" action="<?php echo $action; ?>" method="POST">
	<?php foreach ($inputs as $name => $value) : ?>
		<input type="hidden" name="<?php echo $type.'['.$name.']'; ?>" value="<?php echo htmlspecialchars($value); ?>" />
	<?php endforeach; ?>
		<input type="hidden" name="error" value="<?php echo $input_error; ?>" />
	</form>
	<div style="text-align:center; margin: 100px 0px;"><img src="/admin/img/loading.gif" /></div>
	<script type="text/javascript">
		document.getElementById('form').submit();
	</script>
	
	<?php } else { ?>
	
	<div class="n_error"><p><?php echo $error; ?></p></div>
	
	<?php } ?>
</div>