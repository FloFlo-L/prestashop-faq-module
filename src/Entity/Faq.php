<?php

declare(strict_types=1);

namespace Module\Faq\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Module\Faq\Repository\FaqRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Faq
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id_faq", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var FaqCategory
     *
     * @ORM\ManyToOne(targetEntity="Module\Faq\Entity\FaqCategory", inversedBy="faqs")
     * @ORM\JoinColumn(name="id_faq_category", referencedColumnName="id_faq_category", nullable=false)
     */
    private $faqCategory;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @ORM\OneToMany(targetEntity="Module\Faq\Entity\FaqLang", cascade={"persist", "remove"}, mappedBy="faq")
     */
    private $faqLangs;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date_add", type="datetime")
     */
    private $dateAdd;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date_upd", type="datetime")
     */
    private $dateUpd;

    public function __construct()
    {
        $this->faqLangs = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

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
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     * @return $this
     */
    public function setPosition(int $position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return $this
     */
    public function setActive(bool $active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getFaqLangs()
    {
        return $this->faqLangs;
    }

    /**
     * @param int $langId
     * @return FaqLang|null
     */
    public function getFaqLangByLangId(int $langId)
    {
        foreach ($this->faqLangs as $faqLang) {
            if ($langId === $faqLang->getLang()->getId()) {
                return $faqLang;
            }
        }

        return null;
    }

    /**
     * @param FaqLang $faqLang
     * @return $this
     */
    public function addFaqLang(FaqLang $faqLang)
    {
        $faqLang->setFaq($this);
        $this->faqLangs->add($faqLang);

        return $this;
    }

    /**
     * @param DateTime $dateAdd
     * @return $this
     */
    public function setDateAdd(DateTime $dateAdd)
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateAdd()
    {
        return $this->dateAdd;
    }

    /**
     * @param DateTime $dateUpd
     * @return $this
     */
    public function setDateUpd(DateTime $dateUpd)
    {
        $this->dateUpd = $dateUpd;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateUpd()
    {
        return $this->dateUpd;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setDateUpd(new DateTime());

        if ($this->getDateAdd() == null) {
            $this->setDateAdd(new DateTime());
        }
    }
}
