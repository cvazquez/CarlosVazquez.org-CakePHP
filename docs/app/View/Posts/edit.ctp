<!-- File: /app/View/Posts/edit.ctp -->

<h1>Edit Post</h1>
<?php
$this->Html->script('https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js', array("inline"=>false));
$this->Html->script('https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js', array("inline"=>false));

$this->Html->script('tiny_mce3.5.11/tiny_mce', array("inline"=>false));
$this->Html->script('posts/edit', array("inline"=>false));
$this->Html->script('multiautocomplete/multiautocomplete', array("inline"=>false));

echo $this->Html->css('posts/edit', array("inline"=>false));

?>




<?php

echo $this->Form->create('Post');


echo $this->Form->input('title');

// Allow an empty date if not ready to publish
echo $this->Form->input('publishAt', array('allowEmpty' => true, 'default' => 0, 'empty' => true));


?>


<div class="input select" id="select_categories">
Categories<br>
<ul class="item-list categories">
<?php


// Loop through each category
foreach ($categories AS $categoryId=>$categoryName)
	{
		// If this category is in the array of categories already associated with this post (in the categories_posts table), then set class=active to show the category. Otherwise the category stays hidden and isn't selected
		if (in_array($categoryId,$categoriesSelected))
		{
			$selected = "class='active'";
		}
		else
		{
			$selected = "";
		}

		// Create each category in a list, but only display if class=active is set
		echo '<li item="' . $categoryId. '" title="' . $categoryName . '"' . $selected . '>' . $categoryName;;
			
		// Allow removal of an active category
		echo '<a class="close" title="Remove ' .  $categoryName . '">x</a>';
		echo '</li>';
	}

	// This is an autocomplete input form, that displays available categories. You can also cursor down to display a list
?>
	<li class="input"><input class="item-input" value="" tabindex="2" placeholder="Start typing or press down"></li>
</ul>
<?php //<input type="hidden" id="categories" name="data[CategoryPost][categoryids]" value="">?>
</div>



<?php

if ( count($drafts) > 0 && count_chars($drafts['EntryDrafts']['content'] != count_chars($postBody)))
{
	
	// echo "CONTENT:" . $drafts['EntryDrafts']['content'] . "<br>";
	//echo "BODY: " . $postBody . "<br>";
	
	//echo count_chars(trim(chop($drafts['EntryDrafts']['content'])),3);
	//echo "<br>";
	//echo htmlentities(trim($drafts['EntryDrafts']['content']));
	
	
	//var_dump(trim(chop($drafts['EntryDrafts']['content'])));
	//echo "<br>";
	//echo count_chars(trim(chop($postBody)),3);
	//echo htmlentities($postBody);
	
	//var_dump(trim(chop($postBody)));
	
	//echo "Draft and current content are not equal: View Draft (put thin in an overlay, then ask whether to use. give a history option too)";
	//exit;
}


echo $this->Form->input('body', array(	'rows' => '20',
										'label' => 'Body <span id="ViewDrafts">(View Drafts)</span><span id="SaveBodyDraftStatus"></span>',
										));

echo $this->Form->input('id', array('type' => 'hidden'));
echo $this->Form->end('Save Post');


/*
 echo "<p>Category<br>";
echo $this->Form->select('category_id', $categories, array(
		'empty' => '(choose one)'
));
echo "</p>";
*/
//var_dump($categories);
//echo $this->Form->input('category_id');

/*
 echo $this->Form->input('category', array(
 		'empty' => '(choose one)'
 ));
*/

/* SIngle select, but it doesn't select the correct category */
//echo $this->Form->input('category');
?>