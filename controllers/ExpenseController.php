<?php
class ExpenseController extends Controller {

    public function index() {
        $model = $this->loadModel('Expense');
        $expenses = $model->getAll();
        $this->loadView('expenses/index', ['expenses' => $expenses]);
        $data = ['expenses' => $expenses];
            require_once 'views/expenses/index.php';
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = $this->loadModel('Expense');
            $model->add($_POST['title'], $_POST['amount']);
            header('Location: /');
        } else {
            $this->loadView('expenses/add');
        }
    }

    public function delete($id) {
        $model = $this->loadModel('Expense');
        $model->delete($id);
        header('Location: /');
    }
}
?>