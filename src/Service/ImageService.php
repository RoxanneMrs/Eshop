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

            // this condition is needed because the 'image' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($imageFile) {
    
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $this->slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
    
                // Move the file to the directory where images are stored
                try {
                    $imageFile->move(
                        $directory,
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
    
                // updates the 'imageFilename' property to store the PDF file name
                // instead of its contents
                return $newFilename;
            }

        }
    }

?>