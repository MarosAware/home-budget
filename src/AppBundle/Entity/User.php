<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class User
 * @package AppBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */

class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        $this->budgetPositions = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\BudgetPosition", mappedBy="user", cascade={"remove"})
     */
    private $budgetPositions;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Category", mappedBy="user", cascade={"remove"})
     */
    private $categories;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Photo", mappedBy="user", cascade={"remove"})
     */
    private $photo;


    /**
     * Add budgetPosition
     *
     * @param \AppBundle\Entity\BudgetPosition $budgetPosition
     *
     * @return User
     */
    public function addBudgetPosition(\AppBundle\Entity\BudgetPosition $budgetPosition)
    {
        $this->budgetPositions[] = $budgetPosition;

        return $this;
    }

    /**
     * Remove budgetPosition
     *
     * @param \AppBundle\Entity\BudgetPosition $budgetPosition
     */
    public function removeBudgetPosition(\AppBundle\Entity\BudgetPosition $budgetPosition)
    {
        $this->budgetPositions->removeElement($budgetPosition);
    }

    /**
     * Get budgetPositions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBudgetPositions()
    {
        return $this->budgetPositions;
    }

    /**
     * Add category
     *
     * @param \AppBundle\Entity\Category $category
     *
     * @return User
     */
    public function addCategory(\AppBundle\Entity\Category $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \AppBundle\Entity\Category $category
     */
    public function removeCategory(\AppBundle\Entity\Category $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set photo
     *
     * @param \AppBundle\Entity\Photo $photo
     *
     * @return User
     */
    public function setPhoto(\AppBundle\Entity\Photo $photo = null)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return \AppBundle\Entity\Photo
     */
    public function getPhoto()
    {
        return $this->photo;
    }
}
