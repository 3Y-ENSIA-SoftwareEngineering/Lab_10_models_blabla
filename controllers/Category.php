<?php

class CategoryController extends Controller {

    public function getAll(){
        $model = $this->loadModel('Category'); // Load the Category model
        $categories = $model->getAll(); // Get all categories
        $this->loadView('categories/index', ['categories' => $categories]);
    }

    // add a new category
    public function addCategory(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $model = $this->loadModel('Category'); // load the category model
            $model->add($_POST['name']); // !! suppose the field is name
            header('location: /categories');
        } else {
            $this->loadView('categories/add'); // show the category form
        }
    }

    public function updateCategory($id){
        $model = $this->loadModel('Category'); // Load the category model

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $model->update($id, $_POST['name']); 
            header('location: /categories');

        } else {
            // fetch the current category details
            $category = $model->getById($id);
            $this->loadView('categories/update', ['category' => $category]);
        }
    }

    // delete a category
    public function deleteCategory($id){
        $model = $this->loadModel('Category'); // load the category model
        $model->delete($id); // use the id to delete
        header('location: /categories');
    }

}

?>