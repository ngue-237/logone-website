<?php

namespace App\Command;


use App\Entity\User;
use App\Repository\UserRepository;
use App\utils\CustomValidatorForCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AddAdminUserCmdCommand extends Command
{
    protected static $defaultName = 'app:create-user';
    protected static $defaultDescription = 'Add admin user in database';

    
    private EntityManagerInterface $em;
    private UserRepository $userRepo;
    private UserPasswordEncoderInterface $encoder;
    private CustomValidatorForCommand $validator;
    private SymfonyStyle $io;

    public function __construct(
        CustomValidatorForCommand $validator,
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $encoder,
        UserRepository $userRepo
    )
    {
        parent::__construct();
        $this->em=$em;
        $this->userRepo = $userRepo;
        $this->encoder = $encoder;
        $this->validator = $validator;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('password', InputArgument::REQUIRED, 'user password in plain text')
            ->addArgument('roles', InputArgument::REQUIRED, 'user role')
            // ->addArgument('activationToken', InputArgument::REQUIRED, 'le status du compte utlisateur est (actif)')
        ;
    }

    /**
     * execute after configure() to initialize properties based on inpuy arguments and option
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function intialize(InputInterface $input, OutputInterface $output):void{
    $this->io= new SymfonyStyle($input, $output);
    }

    /**
     * Executed after initializr() and before execute()
     * checks if some of the option/arguments
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    public function interact(InputInterface $input, OutputInterface $output){
        
        //$this->io->section("AJOUT D'UN UTILISATEUR EN BASE DE DONNEES");
        $this->enterEmail($input, $output);
        $this->enterPassword($input, $output);
        $this->enterRoles($input, $output);
        //$this->enterActivationToken($input, $output);

    }

    

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        /** @var string $email */
        $email = $input->getArgument('email');

        if ($email) {
            $io->note(sprintf('You passed an argument: %s', $email));
        }

        /** @var string $password */
        $password = $input->getArgument('password');

        if ($password) {
            $io->note(sprintf('You passed an argument: %s', $password));
        }

        /** @var array<string> $roles */
        $roles = [$input->getArgument('roles')];

        $user = new User();
        $user->setEmail($email)
            ->setPassword($this->encoder->encodePassword($user, $password))
            ->setPasswordConfirm($password)
            ->setLastName("Logone Admin")
            ->setLastName("Logone Admin")
            ->setRgpd(true)
            ->setActivationToken(null)
            ->setRoles($roles)
            ->setCreatedAt(new \DateTime('now'));
        dump($user);
        $this->em->persist($user);
        $this->em->flush();


        $io->success('un utilisateur vien d\' etre creer');

        return Command::SUCCESS;
    }

    private function enterEmail(InputInterface $input, OutputInterface $output){
        $helper = $this->getHelper('question');
        $emailQuestion =new Question("EMAIL DE L'UTILISATEUR: ");
        $email =$helper->ask($input, $output, $emailQuestion);

        $emailQuestion->setValidator([$this->validator,"validateEmail"]);

        if($this->isUserAlreadyExist($email)){
            throw new RuntimeException(sprintf("utilisateur déjà présent en base de donnée avec l'email suivant: %s", $email));
        }
        $input->setArgument('email', $email);
    }

    private function isUserAlreadyExist(string $email): ?User
    {
        return $this->userRepo->findOneBy([
            "email"=>$email
        ]);
    }

    private function enterPassword(InputInterface $input, OutputInterface $output):void{
        $helper = $this->getHelper('question');
        $passwordQuestion =new Question("MOT DE PASSE EN CLAIR DE L'UTILISATEUR: ");

        $passwordQuestion->setValidator([$this->validator,"validatePassword"]);

        $passwordQuestion->setHidden(true)
                        ->setHiddenFallback(false);
                        
        $password =$helper->ask($input, $output, $passwordQuestion);
        $input->setArgument("password", $password);
    }

    private function enterRoles(InputInterface $input, OutputInterface $output):void{
        $helper = $this->getHelper('question');

        $roleQuestion = new ChoiceQuestion("selectionner un role d'utilisateur",[
            "ROLE_USER", "ROLE_ADMIN"
        ],"ROLE_USER");

        $roleQuestion->setErrorMessage("role d'untilisateur invalid");

        $role = $helper->ask($input, $output, $roleQuestion);

        $output->writeln("<info> ROLE UTILISATEUR PRIS EN COMPTE : {$role} </info>");

        $input->setArgument("roles", $role);  
    }

    // private function enterActivationToken(InputInterface $input, OutputInterface $output):void{
    //     $helper = $this->getHelper('question');

    //     $isVerifiedQuestion = new ChoiceQuestion("selectionner un status du compte",[
    //         "INACTIF", "ACTIF"
    //     ],"ACTIF");

    //     $isVerifiedQuestion->setErrorMessage("status d'activation du compte invalid");
    //     $isVerified = $helper->ask($input, $output, $isVerifiedQuestion);

    //     $output->writeln("<info> STATUS D'ACTIVATION  PRIS EN COMPTE : {$isVerified} </info>");

    //     $input->setArgument("isVerified", $isVerified);  
    // }
}
