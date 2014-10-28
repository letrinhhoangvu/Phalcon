<?php 

namespace Phalcon\Mvc\Model\Query {

	/**
	 * Phalcon\Mvc\Model\Query\StatusInterface initializer
	 */
	
	interface StatusInterface {

		/**
		 * \Phalcon\Mvc\Model\Query\Status
		 *
		 * @param boolean $success
		 * @param \Phalcon\Mvc\ModelInterface $model
		 */
		public function __construct($success, $model);


		/**
		 * Returns the model which executed the action
		 *
		 * @return \Phalcon\Mvc\ModelInterface
		 */
		public function getModel();


		/**
		 * Returns the messages produced by a operation failed
		 *
		 * @return \Phalcon\Mvc\Model\MessageInterface[]
		 */
		public function getMessages();


		/**
		 * Allows to check if the executed operation was successful
		 *
		 * @return boolean
		 */
		public function success();

	}
}
