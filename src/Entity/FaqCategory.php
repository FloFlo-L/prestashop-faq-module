<?php

declare(strict_types=1);

namespace Module\Faq\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Module\Faq\Repository\FaqCategoryRepository")
 * @ORM\HasLifecycleCallbacks
 */
class FaqCategory
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id_faq_category", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="icon", type="string", length=255)
     */
    private $icon;

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
     * @ORM\OneToMany(targetEntity="Module\Faq\Entity\FaqCategoryLang", cascade={"persist", "remove"}, mappedBy="faqCategory")
     */
    private $faqCategoryLangs;

    /**
     * @ORM\OneToMany(targetEntity="Module\Faq\Entity\Faq", cascade={"persist", "remove"}, mappedBy="faqCategory")
     */
    private $faqs;

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
        $this->faqCategoryLangs = new ArrayCollection();
        $this->faqs = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ArrayCollection
     */
    public function getFaqCategoryLangs()
    {
        return $this->faqCategoryLangs;
    }

    /**
     * @param int $langId
     * @return FaqCategoryLang|null
     */
    public function getFaqCategoryLangByLangId(int $langId)
    {
        foreach ($this->faqCategoryLangs as $faqCategoryLang) {
            if ($langId === $faqCategoryLang->getLang()->getId()) {
                return $faqCategoryLang;
            }
        }

        return null;
    }

    /**
     * @param FaqCategoryLang $faqCategoryLang
     * @return $this
     */
    public function addFaqCategoryLang(FaqCategoryLang $faqCategoryLang)
    {
        $faqCategoryLang->setFaqCategory($this);
        $this->faqCategoryLangs->add($faqCategoryLang);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getFaqs()
    {
        return $this->faqs;
    }

    /**
     * @param Faq $faq
     * @return $this
     */
    public function addFaq(Faq $faq)
    {
        $faq->setFaqCategory($this);
        $this->faqs->add($faq);

        return $this;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     * @return $this
     */
    public function setIcon(string $icon)
    {
        $this->icon = $icon;

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
