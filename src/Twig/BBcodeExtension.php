<?php

namespace App\Twig;

use Twig\TwigFilter;
use App\Entity\Posts;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Doctrine\ORM\EntityManagerInterface;

class BBcodeExtension extends AbstractExtension

{
    private $search  = array('[b]', '[/b]', '[i]', '[/i]', '[u]', '[/u]', '[strike]', '[/strike]', '[code]', '[/code]', '[quote]', '[/quote]', '[/url]', ':):', ':pfff:', ':o:', ':fou:', ':love:', ':pt1cable:', ':ange:'
,':(:', ':??:', ':jap:', ':heink:', ':sleep:', ':non:', ':D:', ':p:', ':lol:', ':cry:', ':sweat:', ':bounce:', ':;):', ':na:', ':wahoo:', ':whistle:'
, ':hello:', ':ouch:', ':sarcastic:', ':kaola:', ':sol:');
    private $replace = array('<b>', '</b>', '<em>', '</em>', '<u>', '</u>', '<del>', '</del>', '<pre>', '</pre>', '<blockquote>', '</blockquote>', '</a>', '<img src="/build/images/icones/smile.gif" alt=":):" title=":):">', '<img src="/build/images/icones/pfff.gif" alt=":pfff:" title=":pfff:">', '<img src="/build/images/icones/redface.gif" alt=":o:" title=":o:">', '<img src="/build/images/icones/fou.gif" alt=":fou:" title=":fou:">', '<img src="/build/images/icones/love.gif" alt=":love:" title=":love:">', '<img src="/build/images/icones/pt1cable.gif" alt=":pt1cable:" title=":pt1cable:">', '<img src="/build/images/icones/ange.gif" alt=":ange:" title=":ange:">'
    , '<img src="/build/images/icones/frown.gif" alt=":(:" title=":(:">', '<img src="/build/images/icones/confused.gif" alt=":??:" title=":??:">', '<img src="/build/images/icones/jap.gif" alt=":jap:" title=":jap:">', '<img src="/build/images/icones/heink.gif" alt=":heink:" title=":heink:">', '<img src="/build/images/icones/sleep.gif" alt=":sleep:" title=":sleep:">'
    , '<img src="/build/images/icones/non.gif" alt=":non:" title=":non:">', '<img src="/build/images/icones/biggrin.gif" alt=":D:" title=":D:">', '<img src="/build/images/icones/tongue.gif" alt=":p:" title=":p:">', '<img src="/build/images/icones/lol.gif" alt=":lol:" title=":lol:">', '<img src="/build/images/icones/cry.gif" alt=":cry:" title=":cry:">'
    , '<img src="/build/images/icones/sweat.gif" alt=":sweat:" title=":sweat:">', '<img src="/build/images/icones/bounce.gif" alt=":bounce:" title=":bounce:">', '<img src="/build/images/icones/wink.gif" alt=":;):" title=":;):">', '<img src="/build/images/icones/na.gif" alt=":na:" title=":na:">', '<img src="/build/images/icones/wahoo.gif" alt=":wahoo:" title=":wahoo:">'
    , '<img src="/build/images/icones/whistle.gif" alt=":whistle:" title=":whistle:">', '<img src="/build/images/icones/hello.gif" alt=":hello:" title=":hello:">', '<img src="/build/images/icones/ouch.gif" alt=":ouch:" title=":ouch:">', '<img src="/build/images/icones/sarcastic.gif" alt=":sarcastic:" title=":sarcastic:">', '<img src="/build/images/icones/kaola.gif" alt=":kaola:" title=":kaola:">', '<img src="/build/images/icones/sol.gif" alt=":sol:" title=":sol:">');
 
    private $searchRegex  = array('/(\[url=)([^\]]+)(\])/', '/(\[url\])([^\]]+)(\])/', '/(\[img\])(.*)(\[\/img\])/' );
    private $replaceRegex = array('<a target="_blank" href="\2">', '<a target="_blank" href="\2">\2', '<img src="\2" alt="\2" title="\2" />' );

    private $banwords = array('/javascript:/', '/script:/');

    private $manager;
    private $string;
    
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }
 
    public function getFilters()
    {
         return array(
            new TwigFilter('bbCode', array($this, 'bbCodeFilter'), array('is_safe' => array('html'))),
            );
    }
 
     /**
      * Converts BBCode tag into HTML tags
      *
      * @param $string String source
      *
      * @return string
      */
    public function bbCodeFilter()
    {
        $arguments = func_get_args();
        $this->string = array_shift($arguments);
        $this->string = \htmlspecialchars($this->string, ENT_QUOTES, 'UTF-8');
        $this->string = nl2br($this->string);

        $this->parseArguments($arguments);

        $this->string = preg_replace($this->banwords, '', $this->string);

        $this->string = preg_replace_callback('/(\[quotemsg=)([^\]]+)(\])(.*?[\S+\n\r\s]+?)\[\/quotemsg]/',
        //(\[quotemsg=)([^\]]+)(\])((?!.*?[\S+\n\r\s]+?quotemsg=).*?[\S+\n\r\s]+?)\[\/quotemsg]
        function($matches){ return $this->searchpost($matches); },
        $this->string);

        return preg_replace($this->getSearchRegex(), $this->getReplaceRegex(), str_replace($this->getSearch(), $this->getReplace(), $this->string));
    }

    public function searchpost($matches)
    {
        $repo = $this->manager->getRepository(Posts::class);
        $pseudo = $repo->findOneBy(['id' => $matches[2]])->getUser()->getPseudo();
        return ("<div class='msgquote'><table><tbody><tr><td><a class='linkquote' href='/post/".$matches[2]."'><b>".$pseudo." a écrit:</b></a><hr style='margin-top: 1px'><p>".$matches[4]."</p><hr></td></tr/></tbody></table></div>");
    }

    private function parseArguments($arguments)
    {
        foreach ($arguments as $argument) {
            if (is_string($argument)) {
                switch ($argument) {
                    case 'nofollow':
                        $this->replaceRegex[0] = '<a rel="nofollow" href="">';
                        $this->replaceRegex[1] = '<a rel="nofollow" href="">';
                        break;
                    // Can add more cases to add more functionality.
                    default:
                        break;
                }
            }
        }
    }

    // Getter functions

    private function getSearch()
    {
        return $this->search;
    }

    private function getReplace()
    {
        return $this->replace;
    }
 
    private function getSearchRegex()
    {
        return $this->searchRegex;
    }

    private function getReplaceRegex()
    {
        return $this->replaceRegex;
    }

    public function getName()
    {
        return 'bbcode_extension';
    }
}
