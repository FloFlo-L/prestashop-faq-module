<?php

declare(strict_types=1);

namespace Module\Faq\Entity;

use Doctrine\ORM\Mapping as ORM;
use PrestaShopBundle\Entity\Lang;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class FaqLang
{
    /**
     * @var Faq
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Module\Faq\Entity\Faq", inversedBy="faqLangs")
     * @ORM\JoinColumn(name="id_faq", referencedColumnName="id_faq", nullable=false)
     */
    private $faq;

    /**
     * @var Lang
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\Lang")
     * @ORM\JoinColumn(name="id_lang", referencedColumnName="id_lang", nullable=false, onDelete="CASCADE")
     */
    private $lang;

    /**
     * @var string
     *
     * @ORM\Column(name="question", type="string", length=512)
     */
    private $question;

    /**
     * @var string|null
     *
     * @ORM\Column(name="answer", type="text", nullable=true)
     */
    private $answer;

    /**
     * @return Faq
     */
    public function getFaq()
    {
        return $this->faq;
    }

    /**
     * @param Faq $faq
     * @return $this
     */
    public function setFaq(Faq $faq)
    {
        $this->faq = $faq;

        return $this;
    }

    /**
     * @return Lang
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param Lang $lang
     * @return $this
     */
    public function setLang(Lang $lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param string $question
     * @return $this
     */
    public function setQuestion(string $question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * @param string|null $answer
     * @return $this
     */
    public function setAnswer(?string $answer)
    {
        $this->answer = $answer;

        return $this;
    }
}
