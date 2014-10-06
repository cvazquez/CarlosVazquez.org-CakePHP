<?php
class PostsController extends AppController {
	
	public $components = array('RequestHandler');
	
	public function isAuthorized($user) {
		// All registered users can add posts
		if ($this->action === 'add') {
			return true;
		}
	
		// The owner of a post can edit and delete it
		if (in_array($this->action, array('edit', 'delete'))) {
			$postId = (int) $this->request->params['pass'][0];
			if ($this->Post->isOwnedBy($postId, $user['id'])) {
				return true;
			}
		}
	
		return parent::isAuthorized($user);
	}
	
    public function index() {
        $this->set('posts', $this->Post->find('all'));
    }
    
    public function view($id = null) {
        if (!$id) {
            throw new NotFoundException(__('Invalid post'));
        }

        $post = $this->Post->findById($id);
        if (!$post) {
            throw new NotFoundException(__('Invalid post'));
        }
        $this->set('post', $post);
    }
    
    public function add() {
        if ($this->request->is('post')) {
            $this->Post->create();
            if ($this->Post->save($this->request->data)) {
                $this->Session->setFlash(__('Your post has been saved.'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(__('Unable to add your post.'));
        }
    }
    
    public function edit($id = null) {
	    if (!$id) {
        	throw new NotFoundException(__('Invalid post'));
    	}

	    $post = $this->Post->findById($id);
	    if (!$post) {
        	throw new NotFoundException(__('Invalid post'));
    	}
    	

    	/* Form is being submitted. Save data */
    	if ($this->request->is(array('post', 'put'))) {
    		
	        $this->Post->id = $id;
	        $saveStatus = $this->Post->save($this->request->data);
	        
        	if ($saveStatus ) 
        	{
        		// Use the Cake PHP database config values to create a connection to MySQL
        		$this->loadModel('CategoriesPosts');
        		$this->CategoriesPosts->saveCategories($this->request->data["Category"], $this->Post->id);
        		
	            $this->Session->setFlash(__('Your post has been updated.'));
            	return $this->redirect(array('action' => 'index'));
        	}

        	$this->Session->setFlash(__('Unable to update your post.'));
    	}
    	else 
    	{
    		/* Check if a draft exists */
    		$this->loadModel('EntryDrafts');
    		$draft = array();
    		$draft['conditions'] = array('entry_id' => $id);
    		$draft['order'] = 'id desc';
    		$draft['limit'] = 1;
    		
    		$this->set('drafts', $this->EntryDrafts->find('first', $draft));
    		
    		/* Assign content to posts */
    		$this->set('postBody', $post["Post"]["body"]);
    		
    		// Fetch all categories
    		$this->loadModel('Category');
    		$this->set('categories', $this->Category->find('list', array(
    				'order' => array('Category.name' => 'asc'))));
    		 
    		 
    		// Find the categpries already associated with this post
    		$this->loadModel('CategoriesPosts');
    		$conditions = array("post_id" => $id);
    		$fields = array('CategoriesPosts.category_id');
    		$this->set('categoriesSelected', $this->CategoriesPosts->find('list',
    				array(	'conditions' => $conditions,
    						'fields' => $fields)));
    	}

	    if (!$this->request->data) {
        	$this->request->data = $post;
    	}
    	    	    	
    	
    	/* Some code for trying to get the HABTM code to work. I will have to work on it later when I have more time
    	  
    	// Setup query for multi-select of category
    	 
    	/$this->set('categories', $this->Post->Category->find('list', array(
    			'order' => array('Category.name' => 'asc'))));
    	
    	
    	//echo("<pre>");
    	// print_r($this->request->data);
    	//print_r($this->request->data["CategoryPost"]);
    	// print_r($saveStatus);
    	//echo("</pre>");
    	
    	
    	
    	//print_r($database);
    	
    	//print_r($database->default["host"]);
    	//print_r($database->default);
    	
    	echo("<pre>");
    	print_r($mysqli);
    	print_r($this->request->data["Category"]);
    	echo("</pre>");
    	
    	
    	
    	foreach ($this->request->data["Category"] as $category => $v)
    	{
    	echo "$category=>$v[id]<br>";
    	//echo "$this->request->data["Category"][$category]->id);
    	//print_r($this->request->data["Category"][$category]);
    	//echo($this->request->data["Category"][$category] . "<br>");
    	
    	}
    	
    		
    	//$saveCategoryStatus = $this->CategoriesPosts->save($this->request->data["CategoryPost"]);
    	//$saveCategoryStatus = $this->CategoriesPosts->save($this->request->data);
    	//$saveCategoryStatus = $this->Category->save($this->request->data);
    	
    	echo("<pre>");
    	print_r($this->request->data);
    	print_r($saveStatus);
    	echo("</pre>");
    	exit;
    	
    	
    	//$categoryData = array('post_id' => $id, 'category_id' => $this->request->data->CategoryPost->categoryids);
    	//$this->CategoriesPosts->create($categoryData);
    	*/
    	
	}
	
	public function ajaxSaveDraft()
	{
		$message = "";
		
		if ($this->request->is('post') && isset($this->request->data['entry_id']) && isset($this->request->data['content']))
		{			
			/* Save body to a temporary table */
			$this->loadModel('EntryDrafts');
			
			$this->EntryDrafts->create();
			$saveStatus = $this->EntryDrafts->save($this->request->data);
			
			if ($saveStatus)
			{
				$message = 'success';
			}
			else			
			{
				$message = print_r($saveStatus, true);
			}
			
			
			
		}
		else 
		{
			if (!isset($this->request->data['entry_id']))
			{
				$message = 'Post ID is missing';
			}
			elseif (is_nan($this->request->data['entry_id']))
			{
				$message = 'Post ID is not a number';
			}
				
			if (!isset($this->request->data['content'])) {
				$message = 'Post BODY is missing';
			}
			
			
		}
		
		$this->set(array(
				'message' => $message,
				'_serialize' => array('message')
		));
			
		
		
	}
	
	public function getdrafts()
	{
		$message = "No drafts found.";
		
		if ($this->request->is('post') && isset($this->request->data['id']))
		{
			$this->loadModel('EntryDrafts');
			$draftParams = array();
			$draftParams['fields'] = array('id', 'content', 'created');
			$draftParams['conditions'] = array('entry_id' => $this->request->data['id']);
			$draftParams['order'] = 'id desc';
			$draftParams['group'] = 'DATE_FORMAT(created, "%Y-%m-%d %k:%i")';
			
			$message = $this->EntryDrafts->find('all', $draftParams);		
		}
		
		$this->set(array(
				'message' => $message,
				'_serialize' => array('message')
		));
	}
	
	public function getdraft()
	{
		$message = "No draft found.";
	
		if ($this->request->is('post') && isset($this->request->data['id']))
		{
			$this->loadModel('EntryDrafts');
			$draftParams = array();
			$draftParams['fields'] = array('content');
			$draftParams['conditions'] = array('id' => $this->request->data['id']);
			//$draftParams['limit'] = 1;
				
			$message = $this->EntryDrafts->find('all', $draftParams);
				
		}
	
		$this->set(array(
				'message' => $message,
				'_serialize' => array('message')
		));
	}
	
	public function delete($id) 
	{
	    if ($this->request->is('get')) {
    	    throw new MethodNotAllowedException();
	    }

    		if ($this->Post->delete($id)) {
        		$this->Session->setFlash(
            	__('The post with id: %s has been deleted.', h($id))
    	    );
	        return $this->redirect(array('action' => 'index'));
    	}
	}
}
?>