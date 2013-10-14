<?php

namespace Adstacy\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CleanTempAdImageCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('adstacy:clean:temp-ad-image')
            ->setDescription('Clean temporary ad image')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManager();
        $since = date('Y-m-d', strtotime('-1 day', time()));
        $ads = $em->createQuery('
            SELECT a, i
            FROM AdstacyAppBundle:Ad a
            LEFT JOIN a.images i
            WHERE a.updated > :since AND a.type = :type
        ')->setParameter('since', $since)->setParameter('type', 'image')->getResult();

        $imagenames = array();
        foreach ($ads as $ad) {
            $imagenames[] = $ad->getImagename();
            foreach ($ad->getImages() as $image) {
                $imagenames[] = $image->getImagename();
            }
        }

        if (count($imagenames) > 0) {
            $formatted = strtolower("'".implode("', '", $imagenames)."'");
            $images = $em->createQuery("
                SELECT t
                FROM AdstacyAppBundle:TempAdImage t
                WHERE t.imagename NOT IN ($formatted)
            ")->getResult();
            $cnt = 0;
            foreach ($images as $image) {
                $em->remove($image);
                $cnt++;
            }
            $em->flush();
            $output->writeln("$cnt temporary images removed");
        } else {
            $output->writeln('No temporary images');
        }
    }
}
