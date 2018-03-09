<?php
declare(strict_types = 1);
/**
 * /src/Entity/DateDimension.php
 *
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
namespace App\Entity;

use App\Entity\Traits\Blameable;
use App\Entity\Traits\Timestampable;
use App\Security\RolesService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use function mb_strlen;
use function random_int;
use function array_unique;
use function array_merge;

/**
 * Class ApiKey
 *
 * @AssertCollection\UniqueEntity("token")
 *
 * @ORM\Table(
 *      name="api_key",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="uq_token", columns={"token"}),
 *      },
 *  )
 * @ORM\Entity()
 *
 * @package App\Entity
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
class ApiKey implements EntityInterface
{
    // Traits
    use Blameable;
    use Timestampable;

    /**
     * @var string
     *
     * @Groups({
     *      "ApiKey",
     *      "ApiKey.id",
     *  })
     *
     * @ORM\Column(
     *      name="id",
     *      type="guid",
     *      nullable=false,
     *  )
     * @ORM\Id()
     */
    private $id;

    /**
     * @var string
     *
     * @Groups({
     *      "ApiKey",
     *      "ApiKey.token",
     *  })
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Length(min = 40, max = 40)
     *
     * @ORM\Column(
     *      name="token",
     *      type="string",
     *      length=40,
     *      nullable=false
     *  )
     */
    private $token = '';

    /**
     * @var string
     *
     * @Groups({
     *      "ApiKey",
     *      "ApiKey.description",
     *  })
     *
     * @ORM\Column(
     *      name="description",
     *      type="text",
     *  )
     */
    private $description = '';

    /**
     * @var Collection<UserGroup>|ArrayCollection<UserGroup>
     *
     * @Groups({
     *      "ApiKey.userGroups",
     *  })
     *
     * @ORM\ManyToMany(
     *      targetEntity="UserGroup",
     *      inversedBy="apiKeys",
     *  )
     * @ORM\JoinTable(
     *      name="api_key_has_user_group"
     *  )
     */
    private $userGroups;

    /**
     * @var Collection<LogRequest>
     *
     * @Groups({
     *      "ApiKey.logsRequest",
     *  })
     *
     * @ORM\OneToMany(
     *      targetEntity="App\Entity\LogRequest",
     *      mappedBy="apiKey",
     *  )
     */
    private $logsRequest;

    /**
     * ApiKey constructor.
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
        $this->userGroups = new ArrayCollection();
        $this->logsRequest = new ArrayCollection();

        $this->generateToken();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     *
     * @return ApiKey
     */
    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return ApiKey
     */
    public function generateToken(): self
    {
        $random = '';
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = mb_strlen($chars, '8bit') - 1;

        for ($i = 0; $i < 40; ++$i) {
            $random .= $chars[random_int(0, $max)];
        }

        return $this->setToken($random);
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return ApiKey
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<App\Entity\UserGroup>|ArrayCollection<App\Entity\UserGroup>
     */
    public function getUserGroups(): Collection
    {
        return $this->userGroups;
    }

    /**
     * Getter for user request log collection.
     *
     * @return Collection<App\Entity\LogRequest>|ArrayCollection<App\Entity\LogRequest>
     */
    public function getLogsRequest(): Collection
    {
        return $this->logsRequest;
    }

    /**
     * Getter for roles.
     *
     * @Groups({
     *      "ApiKey.roles",
     *  })
     *
     * @return string[]
     */
    public function getRoles(): array
    {
        /**
         * Lambda iterator to get user group role information.
         *
         * @param UserGroup $userGroup
         *
         * @return string
         */
        $iterator = function (UserGroup $userGroup) {
            return $userGroup->getRole()->getId();
        };

        return array_unique(array_merge([RolesService::ROLE_API], $this->userGroups->map($iterator)->toArray()));
    }

    /**
     * Method to attach new userGroup to current api key.
     *
     * @param UserGroup $userGroup
     *
     * @return ApiKey
     */
    public function addUserGroup(UserGroup $userGroup): self
    {
        if (!$this->userGroups->contains($userGroup)) {
            $this->userGroups->add($userGroup);
            $userGroup->addApiKey($this);
        }

        return $this;
    }

    /**
     * Method to remove specified userGroup from current api key.
     *
     * @param UserGroup $userGroup
     *
     * @return ApiKey
     */
    public function removeUserGroup(UserGroup $userGroup): self
    {
        if ($this->userGroups->removeElement($userGroup)) {
            $userGroup->removeApiKey($this);
        }

        return $this;
    }

    /**
     * Method to remove all many-to-many userGroup relations from current api key.
     *
     * @return ApiKey
     */
    public function clearUserGroups(): self
    {
        $this->userGroups->clear();

        return $this;
    }
}
