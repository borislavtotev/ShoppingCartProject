<?php
namespace SoftUni\Application\Areas\Users\Controllers;

include_once('..' . DIRECTORY_SEPARATOR . 'Framework' . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'Controller.php');

//use SoftUni\Application\Areas\Users\ViewModel\User;
use SoftUni\Application\Areas\Users\Models\User;
use SoftUni\Application\Areas\Users\ViewModel\UserViewModel;
use SoftUni\View;
use SoftUni\Controllers\Controller;
use SoftUni\Application\Areas\Users\ViewModel\LoginInformation;
use SoftUni\Application\Areas\Users\ViewModel\RegisterInformation;

/**
 * @Route("user")
 */
class UsersController extends Controller
{
    /**
     * @Route(login)
     * @POST
     */
    public function login()
    {
        $viewModel = new LoginInformation();

        if (isset($_POST['username'], $_POST['password'])) {
            try {
                $user = $_POST['username'];
                $pass = $_POST['password'];

                $this->initLogin($user, $pass);
            } catch (\Exception $e) {
                $viewModel->error = $e->getMessage();
                return new View($viewModel);
            }
        }

        return new View($viewModel);
    }

    /**
     * @Authorize
     * @Route("register")
     * @POST
     */
    public function register()
    {
        $viewModel = new RegisterInformation();

        if (isset($_POST['username'], $_POST['password'])) {
            try {
                $user = $_POST['username'];
                $pass = $_POST['password'];

                $userModel = new User();
                $userModel->register($user, $pass);

                $this->initLogin($user, $pass);
            } catch (\Exception $e) {
                $viewModel->error = $e->getMessage();
                return new View($viewModel);
            }
        }

        return new View();
    }

    /**
     * @Route("Profile")
     * @GET
     */
    public function profile()
    {
        if (!$this->isLogged()) {
            header("Location: login");
        }

        $userModel = new User();
        $userInfo = $userModel->getInfo($_SESSION['id']);


        $userViewModel = new UserViewModel(
            $userInfo['username'],
            $userInfo['password'],
            $userInfo['id'],
            $userInfo['gold'],
            $userInfo['food']
        );

        if (isset($_POST['edit'])) {
            if ($_POST['password'] != $_POST['confirm'] || empty($_POST['password'])) {
                $userViewModel->error = 1;
                return new View($userViewModel);
            }

            if ($userModel->edit(
                $_POST['username'],
                $_POST['password'],
                $_SESSION['id']
            )) {
                $userViewModel->success = 1;
                $userViewModel->setUsername($_POST['username']);
                $userViewModel->setPass($_POST['password']);

                return new View($userViewModel);
            }

            $userViewModel->error = 1;
            return new View($userViewModel);
        }

        return new View($userViewModel);
    }

    private function initLogin($user, $pass)
    {
        $userModel = new User();

        $userId = $userModel->login($user, $pass);
        $_SESSION['id'] = $userId;
        header("Location: profile");
    }
}