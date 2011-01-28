<?php
namespace kateglo\application\models;
/*
 *  $Id$
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the GPL 2.0. For more information, see
 * <http://code.google.com/p/kateglo/>.
 */
use Doctrine\Common\Collections\ArrayCollection;
/**
 *
 *
 * @package kateglo\application\models
 * @license <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html> GPL 2.0
 * @link http://code.google.com/p/kateglo/
 * @since $LastChangedDate$
 * @version $LastChangedRevision$
 * @author  Arthur Purnama <arthur@purnama.de>
 * @copyright Copyright (c) 2009 Kateglo (http://code.google.com/p/kateglo/)
 *
 * @Entity
 * @Table(name="language")
 */
class Language {
	
	const CLASS_NAME = __CLASS__;
	
	/**
	 * @var int
	 * @Id
	 * @Column(type="integer", name="language_id")
	 * @GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 *
	 * @var string
	 * @Column(type="string", name="language_name", unique=true, length=255)
	 */
	protected $language;
	
	/**
	 * @var Doctrine\Common\Collections\ArrayCollection
	 * @OneToMany(targetEntity="kateglo\application\models\Foreign", mappedBy="language", cascade={"persist"})
	 */
	protected $foreigns;
	
	/**
	 * 
	 * Construct
	 */
	function __construct() {
		$this->foreigns = new ArrayCollection ();
	}
	
	/**
	 * @return the $id
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return the $language
	 */
	public function getLanguage() {
		return $this->language;
	}
	
	/**
	 * @param string $language
	 */
	public function setLanguage($language) {
		$this->language = $language;
	}
	
	/**
	 *
	 * @param kateglo\application\models\Foreign $foreign
	 * @return void
	 */
	public function addForeign(Foreign $foreign) {
		$this->foreigns [] = $foreign;
		$foreign->setLanguage ( $this );
	}
	
	/**
	 *
	 * @param kateglo\application\models\Foreign $foreign
	 * @return void
	 */
	public function removeForeign(Foreign $foreign) {
		/*@var $removed kateglo\application\models\Foreign */
		$removed = $this->foreigns->removeElement ( $foreign );
		if ($removed !== null) {
			$removed->removeLanguage ();
		}
	}
	
	/**
	 *
	 * @return Doctrine\Common\Collections\ArrayCollection
	 */
	public function getForeigns() {
		return $this->foreigns;
	}
}

?>