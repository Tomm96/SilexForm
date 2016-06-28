<?php
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/UserData/User.php';
require_once __DIR__.'/Checker/PhoneNumber.php';
require_once __DIR__.'/Checker/PhoneNumberValidator.php';
require_once __DIR__.'/Checker/Name.php';
require_once __DIR__.'/Checker/NameValidator.php';
require_once __DIR__.'/ServiceProvider/PhoneValidatorServiceProvider.php';
require_once __DIR__.'/ServiceProvider/NameValidatorServiceProvider.php';

use ServiceProvider\PhoneValidatorServiceProvider;
use ServiceProvider\NameValidatorServiceProvider;
use Silex\Provider\FormServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\Validator\Constraints as Assert;
use UserData\User;

$app = new Silex\Application();

$app['debug'] = true;

$app->get('/view', function (Silex\Application $app)  {
    $rows = $app['db']->fetchAll('SELECT * FROM users');

    return $app['twig']->render(
        'view.html.twig',
        array(
            'users' => $rows,
        )
    );
})->bind("view");

$app->get('/', function (Silex\Application $app)  {

    return $app['twig']->render(
        'index.html.twig',
        array(
            'nothing' => "",
        )
    );
})->bind("home");

$app->match('/edit/{id}', function (Request $request, $id) use ($app)  {
    $sql = "SELECT * FROM users WHERE id = ?";
    $post = $app['db']->fetchAssoc($sql, array((int) $id));

    if (!$post) return $app->redirect($app["url_generator"]->generate("view"));

    $user = $post;

    $form = $app['form.factory']->createBuilder('form', $user)
        ->add('first_name', 'text', array(
            'constraints' => array(new Assert\Length(array('min' => 3)), new Checker\Name()),
            'attr' => array('class' => 'form-control', 'placeholder' => 'Įveskite vardą'),
            'label' => 'Vardas',
            'required' => false
        ))
        ->add('last_name', 'text', array(
            'constraints' => array(new Assert\Optional(), new Checker\Name()),
            'attr' => array('class' => 'form-control', 'placeholder' => 'Įveskite pavardę'),
            'label' => 'Pavardė',
            'required' => false
        ))
        ->add('email', 'email', array(
            'constraints' => array(new Assert\Email()),
            'attr' => array('class' => 'form-control', 'placeholder' => 'pavyzdys@pavyzdys.com'),
            'label' => 'El. paštas',
            'required' => false
        ))
        ->add('phone_number', 'text', array(
            'constraints' => array(new Checker\PhoneNumber()),
            'attr' => array('class' => 'form-control', 'placeholder' => '+370'),
            'label' => 'Telefono numeris',
            'required' => false
        ))
        ->add('comment', 'textarea', array(
            'constraints' => array(new Assert\Length(array('max' => 50))),
            'attr' => array('class' => 'form-control', 'placeholder' => 'Jusu komentaras'),
            'label' => 'Komentaras',
            'required' => false
        ))
        ->getForm();

    $form->handleRequest($request);

    if ($form->isValid()) {
        $conn = DriverManager::getConnection($app['db.options']);
        $queryBuilder = $conn->createQueryBuilder();

        $queryBuilder
            ->update('users', 'u')
            ->set('u.first_name', '?')
            ->set('u.last_name', '?')
            ->set('u.email', '?')
            ->set('u.phone_number', '?')
            ->set('u.comment', '?')
            ->where('id=?')
            ->setParameter(0, $user->getFirstName() ? $user->getFirstName() : $post['first_name'])
            ->setParameter(1, $user->getLastName() ? $user->getLastName() : $post['last_name'])
            ->setParameter(2, $user->getEmail() ? $user->getEmail() : $post['email'])
            ->setParameter(3, $user->getPhoneNumber() ? $user->getPhoneNumber() : $post['phone_number'])
            ->setParameter(4, $user->getComment() ? $user->getComment() : $post['comment'])
            ->setParameter(5, $post['id'])
        ;
        $queryBuilder->execute();

        return $app->redirect($app["url_generator"]->generate("view"));
    }


    return $app['twig']->render(
        'edit.html.twig',
        array(
            'cuser' => $post,
            'form' => $form->createView()
        )
    );
});
$app->register(new Silex\Provider\LocaleServiceProvider());

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/templates',
));

//$app->register(new Silex\Provider\ValidatorServiceProvider());

$app->register(new PhoneValidatorServiceProvider());
$app->register(new NameValidatorServiceProvider());

$app->register(new Silex\Provider\ValidatorServiceProvider(), array(
    'validator.validator_service_ids' => array(
        'validator.phonenumber' => 'validator.phonenumber',
        'validator.name' => 'validator.name'
    )
));

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'     => 'pdo_mysql',
        'dbname'     => 'pigu',
        'host'       => 'localhost',
        'user'       => 'root',
        'password'   => 'tomas369',
        'charset'    => 'utf8mb4',
    ),
));

//$app->register(new Silex\Provider\ValidatorServiceProvider());

//$app->register(new Silex\Provider\ValidatorServiceProvider(), array(
//    'validator.validator_service_ids' => array(
//        'validator.phonenumber' => 'validator.phonenumber',
//    )
//));

$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'translator.domains' => array(),
));

$app->register(new FormServiceProvider());


$app->match('/insert', function(Request $request) use ($app) {
    $user = new User();

    $form = $app['form.factory']->createBuilder('form', $user)
        ->add('first_name', 'text', array(
            'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 3)), new Checker\Name()),
            'attr' => array('class' => 'form-control', 'placeholder' => 'Įveskite vardą'),
            'label' => 'Vardas'
        ))
        ->add('last_name', 'text', array(
            'constraints' => array(new Assert\Optional(), new Checker\Name()),
            'attr' => array('class' => 'form-control', 'placeholder' => 'Įveskite pavardę'),
            'label' => 'Pavardė',
            'required' => false
        ))
        ->add('email', 'email', array(
            'constraints' => array(new Assert\NotBlank(), new Assert\Email()),
            'attr' => array('class' => 'form-control', 'placeholder' => 'pavyzdys@pavyzdys.com'),
            'label' => 'El. paštas'
        ))
        ->add('phone_number', 'text', array(
            'constraints' => array(new Checker\PhoneNumber()),
            'attr' => array('class' => 'form-control', 'placeholder' => '+370'),
            'label' => 'Telefono numeris',
            'required' => false
        ))
        ->add('comment', 'textarea', array(
            'constraints' => array(new Assert\Length(array('max' => 50))),
            'attr' => array('class' => 'form-control', 'placeholder' => 'Jusu komentaras'),
            'label' => 'Komentaras',
            'required' => false
        ))
        ->getForm();

    $form->handleRequest($request);

    if ($form->isValid()) {
        $conn = DriverManager::getConnection($app['db.options']);
        $queryBuilder = $conn->createQueryBuilder();

        $queryBuilder
            ->insert('users')
            ->values(
                array(
                    'first_name' => '?',
                    'last_name' => '?',
                    'email' => '?',
                    'phone_number' => '?',
                    'comment' => '?'
                )
            )
            ->setParameter(0, $user->getFirstName())
            ->setParameter(1, $user->getLastName())
            ->setParameter(2, $user->getEmail())
            ->setParameter(3, $user->getPhoneNumber())
            ->setParameter(4, $user->getComment())
        ;
        $queryBuilder->execute();

        return $app->redirect('view');
    }

    // display the form
    return $app['twig']->render('insert.html.twig', array('form' => $form->createView()));
});


$app->run();
?>