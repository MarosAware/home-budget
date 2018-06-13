<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 */
class Category implements \JsonSerializable
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotNull()
     * @Assert\Length(max="255", maxMessage="Category name too long. Allowed 255 characters.")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     * @Assert\Choice(choices={"przychód", "wydatek"}, message="Choose a valid type.")
     */
    private $type;

    /**
     * @var integer
     * @ORM\Column(name="amount", type="integer")
     * @Assert\Type(type="numeric")
     */
    private $amount;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\BudgetPosition", mappedBy="category", cascade={"remove"})
     */
    private $budgetPositions;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="categories")
     * @Assert\NotNull()
     */
    private $user;

    public function __construct()
    {
        $this->budgetPositions = new ArrayCollection();
        $this->amount = 0;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Category
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add budgetPosition
     *
     * @param \AppBundle\Entity\BudgetPosition $budgetPosition
     *
     * @return Category
     */
    public function addBudgetPositions(\AppBundle\Entity\BudgetPosition $budgetPosition)
    {
        $this->budgetPositions[] = $budgetPosition;

        return $this;
    }

    /**
     * Remove budgetPosition
     *
     * @param \AppBundle\Entity\BudgetPosition $budgetPosition
     */
    public function removeBudgetPositions(\AppBundle\Entity\BudgetPosition $budgetPosition)
    {
        $this->budgetPositions->removeElement($budgetPosition);
    }

    /**
     * Get budgetPosition
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBudgetPositions()
    {
        return $this->budgetPositions;
    }

    /**
     * Add budgetPosition
     *
     * @param \AppBundle\Entity\BudgetPosition $budgetPosition
     *
     * @return Category
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

    public function jsonSerialize()
    {
        return [
          'id' => $this->id,
          'name' => $this->name,
          'type' => $this->type,
            'amount' => $this->amount
        ];
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Category
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     *
     * @return Category
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }
}
