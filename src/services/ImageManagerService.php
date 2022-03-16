<?php
namespace App\services;

use App\Entity\CategoryArticle;
use App\Entity\Images;
use App\Entity\CategoryService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

Class ImageManagerService{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }


    public function uploadImageCategoryArt(
        array $images,
        CategoryArticle $category
    )
    {
        // On boucle sur les images
        foreach($images as $image){
            // On génère un nouveau nom de fichier
            $fichier = md5(uniqid()).'.'.$image->guessExtension();
            
            // On copie le fichier dans le dossier uploads
            $image->move(
                $this->params->get('images_directory'),
                $fichier
            );
            
            // On crée l'image dans la base de données
            $img = new Images();
            $img->setName($fichier);
            $category->addImage($img);
        }
    }
    public function uploadImageCategory(
        array $images,
        CategoryService $category
    )
    {
        // On boucle sur les images
        foreach($images as $image){
            // On génère un nouveau nom de fichier
            $fichier = md5(uniqid()).'.'.$image->guessExtension();
            
            // On copie le fichier dans le dossier uploads
            $image->move(
                $this->params->get('images_directory'),
                $fichier
            );
            
            // On crée l'image dans la base de données
            $img = new Images();
            $img->setName($fichier);
            $category->addImage($img);
        }
    }
}