<?php

namespace Infinitube\VideoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('VideoBundle:Default:index.html.twig', array('searchText' => ''));
    }

    public function searchAction()
    {
        return $this->render('VideoBundle:Default:search.html.twig');
    }

    public function watchAction()
    {
        if ($_GET['v']) {
            $videoId = $_GET['v'];
            $url = "http://gdata.youtube.com/feeds/api/videos/" . $videoId . "/related?v=2&alt=jsonc";
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $return = curl_exec($curl);
            curl_close($curl);
            $result = json_decode($return, true);

            $url = 'https://gdata.youtube.com/feeds/api/videos/' . $videoId . '?v=2&alt=jsonc';
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $return = curl_exec($curl);
            curl_close($curl);
            $videoObj = json_decode($return, true);
        } else {
            header('Location: /');
        }
        $videoIds = array();
        $max = 0;
        foreach ($result['data']['items'] as $idx => $video) {
            $videoIds[] = $video['id'];
            $videoProbability[$idx] = $videoObj['data']['category']==$video['category']?20:1;
        }

        $prob = rand(0, max($videoProbability));
        $numElements = (count($result['data']['items'])-1);
        do {
            $el = rand(0, $numElements);
        } while ($prob > $videoProbability[$el]);
        $nextVideoId = $videoIds[$el];

        return $this->render('VideoBundle:Default:watch.html.twig');
    }
}
