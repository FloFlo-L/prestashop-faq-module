<?php

declare(strict_types=1);

namespace Module\Faq\Entity;

use Doctrine\ORM\Mapping as ORM;
use PrestaShopBundle\Entity\Lang;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class FaqCategoryLang
{
    /**
     * @var FaqCategory
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Module\Faq\Entity\FaqCategory", inversedBy="faqCategoryLangs")
     * @ORM\JoinColumn(name="id_faq_category", referencedColumnName="id_faq_category", nullable=false)
     */
    private $faqCategory;

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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @return FaqCategory
     */
    public function getFaqCategory()
    {
        return $this->faqCategory;
    }

    /**
     * @param FaqCategory $faqCategory
     * @return $this
     */
    public function setFaqCategory(FaqCategory $faqCategory)
    {
        $this->faqCategory = $faqCategory;

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }
}
