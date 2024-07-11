<?php

    namespace App\Service;
    use Symfony\Component\HttpFoundation\File\Exception\FileException;
    use Symfony\Component\String\Slugger\SluggerInterface;

    class ImageService {

        // injection de dépendance depuis le constructeur car j'ai besoin de l'interface slugger
        public function __construct(private SluggerInterface $slugger) {
        }

        public function copyImage($name, $directory, $form) {

            $imageFile = $form->get('picture')->getData();

            if ($imageFile) {
    
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        
                $safeFilename = $this->slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
    
                try {
                    $imageFile->move(
                        $directory,
                        $newFilename
                    );
                } catch (FileException $e) {
            
                }
    
                return $newFilename;
            }

        }
    }

?>