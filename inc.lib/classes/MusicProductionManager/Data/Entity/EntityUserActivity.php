<?php

namespace MusicProductionManager\Data\Entity;

use MagicObject\MagicObject;

/**
 * @Entity
 * @JSON(property-naming-strategy=SNAKE_CASE)
 * @Table(name="user_activity")
 */
class EntityUserActivity extends MagicObject
{
	/**
	 * User Activity ID
	 * @Id
	 * @GeneratedValue(strategy=GenerationType.UUID)
	 * @NotNull
	 * @Column(name="user_activity_id", type="varchar(40)", length=40, nullable=false)
	 * @Label(content="User Activity ID")
	 * @var string
	 */
	protected $userActivityId;

	/**
	 * Name
	 * @Column(name="name", type="varchar(255)", length=255, nullable=true)
	 * @Label(content="Name")
	 * @var string
	 */
	protected $name;

	/**
	 * User ID
	 * @Column(name="user_id", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="User ID")
	 * @var string
	 */
	protected $userId;

    /**
     * User
     * 
     * @Label(content="User")
     * @var EntityUser
     * @JoinColumn (name=user_id)
     */
    protected $user;

	/**
	 * Path
	 * @Column(name="path", type="text", nullable=true)
	 * @Label(content="Path")
	 * @var string
	 */
	protected $path;

	/**
	 * Method
	 * @Column(name="method", type="varchar(10)", length=10, nullable=true)
	 * @Label(content="Method")
	 * @var string
	 */
	protected $method;

	/**
	 * Get Data
	 * @Column(name="get_data", type="longtext", nullable=true)
	 * @Label(content="Get Data")
	 * @var string
	 */
	protected $getData;

	/**
	 * Post Data
	 * @Column(name="post_data", type="longtext", nullable=true)
	 * @Label(content="Post Data")
	 * @var string
	 */
	protected $postData;

	/**
	 * Request Body
	 * @Column(name="request_body", type="longtext", nullable=true)
	 * @Label(content="Request Body")
	 * @var string
	 */
	protected $requestBody;

	/**
	 * Time Create
	 * @Column(name="time_create", type="timestamp", length=19, nullable=true, updatable=false)
	 * @Label(content="Time Create")
	 * @var string
	 */
	protected $timeCreate;

	/**
	 * Ip Create
	 * @Column(name="ip_create", type="varchar(50)", length=50, nullable=true, updatable=false)
	 * @Label(content="Ip Create")
	 * @var string
	 */
	protected $ipCreate;

}