<?php
    $userController = new \HawkerHub\Controllers\UserController();

    $userId=-1;
    if ($userController->isLoggedIn()) {
        $userId = $_SESSION['userId'];
    }
    $item = \HawkerHub\Models\ItemModel::findByItemId($itemId, $userId, $userController->getAllFacebookFriendsId());
?>
<!DOCTYPE html>
<html lang="en">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# hawker-hub: http://ogp.me/ns/fb/hawker-hub#">
    <meta charset="UTF-8">
    <title>HawkerHub</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="fb:app_id" content="1466024120391100" /> 
    <meta property="og:type"   content="hawker-hub:food" /> 
    <meta property="og:image"  content="<?php echo $item->photoURL; ?>" />  
    <meta property="og:url" content="http://hawkerhub.quanyang.me/food/<?php echo $itemId;?>" />
    <meta property="og:title" content="<?php echo $item->itemName; ?>" />
    <meta property="og:description" content="<?php echo $item->caption; ?>" />
</head>
<FRAMESET rows="*,0">
    <FRAME src="http://hawkerhub.quanyang.me/#/food/<?php echo $itemId;?>" frameborder="0" noresize>
    <NOFRAMES>
       Your browser does not support frames.
    </NOFRAMES>
</FRAMESET>
</html>