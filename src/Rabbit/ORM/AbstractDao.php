<?php
namespace Rabbit\ORM;

use Rabbit\ORM\Exception\ORMException;

/**
 * Class AbstractDao
 * @package Rabbit\ORM
 */
class AbstractDao {

	protected $entity;

	protected $entityManager;

	public function __construct($dataSourceName='default') {
		$this->entityManager = EntityManager::getForDataSource($dataSourceName);
	}

	/**
	 * @return mixed
	 */
	public function getEntityManager() {
		return $this->entityManager;
	}

	public function __call($name, array $args){
		if(method_exists($this->entityManager, $name))
			return  call_user_func_array(array($this->entityManager, $name), $args);

		throw new ORMException(sprintf('O metodo <strong>%s</strong> não foi encontrado',$name));
	}

	public function load($id){
		return $this->entityManager->find($this->entity,$id);
	}

	public function listAll($limit=100,$offset=0) {
		$qb = $this->entityManager->createQueryBuilder()
				   ->select('e')
				   ->from($this->entity,'e');

		return $this->entityManager->createQuery($qb->getDQL())
					->setMaxResults($limit)
					->setFirstResult($offset)
					->getResult();
	}

	public function insert($entity){
		$this->entityManager->persist($entity);
		$this->entityManager->flush();
	}

	public function save($entity){
		$return = $this->entityManager->merge($entity);
		$this->entityManager->flush();
		return $return;
	}

	public function delete($entity){
		$this->entityManager->remove($entity);
		$this->entityManager->flush();
	}

}