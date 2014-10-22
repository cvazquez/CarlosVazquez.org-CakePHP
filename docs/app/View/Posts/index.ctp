<!-- File: /app/View/Posts/index.ctp -->

<h1>Blog posts</h1>
<p><?php 	echo $this->Html->link('Add Post', array('action' => 'add'));
			echo "&nbsp;&nbsp;"; 
			echo $this->Html->link('Logout', array('action' => 'logout', 'controller' => 'users'));
			echo $this->Html->css('posts/index', array("inline"=>false));
	?>
</p>
<table>
    <tr>
        <th>Id</th>
        <th>Title</th>
        <th>Actions</th>
        <th>Created</th>
        <th>Published</th>
        <th>Deleted</th>
    </tr>

<!-- Here's where we loop through our $posts array, printing out post info -->

    <?php foreach ($posts as $post): ?>
    <tr>
        <td><?php echo $post['Post']['id']; ?></td>
        <td <?php if ($post['Post']['publishAt'] == '' || $post['Post']['deletedAt'] != "")
        			{ 
        				echo "class=\"DeactivatedRow\""; 
        			}
        	?>
        >
            <?php
                echo $this->Html->link(
                    $post['Post']['title'],
                    array('action' => 'view', $post['Post']['id'])
                );
            ?>
        </td>
        <td>
            <?php
            	if ($post['Post']['deletedAt'] == "")
            	{
	                echo $this->Form->postLink(
	                    'Delete',
	                    array('action' => 'delete', $post['Post']['id']),
	                    array('confirm' => 'Are you sure?')
	                );
            	}
            	else
            	{
            		echo $this->Form->postLink(
            				'Reactivate',
            				array('action' => 'reactivate', $post['Post']['id']));
            	}
            ?>
            <?php
                echo $this->Html->link(
                    'Edit', array('action' => 'edit', $post['Post']['id'])
                );
            ?>
        </td>
        <td <?php if ($post['Post']['publishAt'] == '' || $post['Post']['deletedAt'] != "")
        			{ 
        				echo "class=\"DeactivatedRow\""; 
        			}
        	?>>
            <?php echo $post['Post']['created']; ?>
        </td>
        <td <?php if ($post['Post']['publishAt'] == '' || $post['Post']['deletedAt'] != "")
        			{ 
        				echo "class=\"DeactivatedRow\""; 
        			}
        	?>>
            <?php echo $post['Post']['publishAt']; ?>
        </td>
        <td>
            <?php echo $post['Post']['deletedAt']; ?>
        </td>
    </tr>
    <?php endforeach; ?>

</table>