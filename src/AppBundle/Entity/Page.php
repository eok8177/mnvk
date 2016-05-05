<?php

namespace AppBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Page
 *
 * @ORM\Table(name="mnvk_static")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PageRepository")
 */
class Page
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Page", mappedBy="parent")
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="children")
     */
    private $parent;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $fimage;

    /**
     * @var string
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $position = 0;

    /**
     * @var string
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $allowDelete = true;


    /**
     * @var string
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $enabled = false;


    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(type="string", nullable=true, unique=true)
     */
    private $slug;


    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $metaT;


    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $metaD;


    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $metaK;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Page
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return Page
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set fimage
     *
     * @param string $fimage
     *
     * @return Page
     */
    public function setFimage($fimage)
    {
        $this->fimage = $fimage;

        return $this;
    }

    /**
     * Get fimage
     *
     * @return string
     */
    public function getFimage()
    {
        return $this->fimage;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return Page
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set allowDelete
     *
     * @param boolean $allowDelete
     *
     * @return Page
     */
    public function setAllowDelete($allowDelete)
    {
        $this->allowDelete = $allowDelete;

        return $this;
    }

    /**
     * Get allowDelete
     *
     * @return boolean
     */
    public function getAllowDelete()
    {
        return $this->allowDelete;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return Page
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Page
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set metaT
     *
     * @param string $metaT
     *
     * @return Page
     */
    public function setMetaT($metaT)
    {
        $this->metaT = $metaT;

        return $this;
    }

    /**
     * Get metaT
     *
     * @return string
     */
    public function getMetaT()
    {
        return $this->metaT;
    }

    /**
     * Set metaD
     *
     * @param string $metaD
     *
     * @return Page
     */
    public function setMetaD($metaD)
    {
        $this->metaD = $metaD;

        return $this;
    }

    /**
     * Get metaD
     *
     * @return string
     */
    public function getMetaD()
    {
        return $this->metaD;
    }

    /**
     * Set metaK
     *
     * @param string $metaK
     *
     * @return Page
     */
    public function setMetaK($metaK)
    {
        $this->metaK = $metaK;

        return $this;
    }

    /**
     * Get metaK
     *
     * @return string
     */
    public function getMetaK()
    {
        return $this->metaK;
    }

    /**
     * Add child
     *
     * @param \AppBundle\Entity\Page $child
     *
     * @return Page
     */
    public function addChild(\AppBundle\Entity\Page $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \AppBundle\Entity\Page $child
     */
    public function removeChild(\AppBundle\Entity\Page $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param \AppBundle\Entity\Page $parent
     *
     * @return Page
     */
    public function setParent(\AppBundle\Entity\Page $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \AppBundle\Entity\Page
     */
    public function getParent()
    {
        return $this->parent;
    }
}
