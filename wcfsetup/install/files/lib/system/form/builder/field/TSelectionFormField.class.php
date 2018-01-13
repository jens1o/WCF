<?php
namespace wcf\system\form\builder\field;
use wcf\system\form\builder\field\validation\FormFieldValidationError;

/**
 * Provides default implementations of `ISelectionFormField` methods.
 * 
 * @author	Matthias Schmidt
 * @copyright	2001-2018 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	WoltLabSuite\Core\System\Form\Builder\Field
 * @since	3.2
 */
trait TSelectionFormField {
	/**
	 * possible options to select
	 * @var	null|array
	 */
	protected $__options;
	
	/**
	 * possible values of the selection
	 * @var	array 
	 */
	protected $possibleValues = [];
	
	/**
	 * Returns the possible options of this field.
	 * 
	 * @return	array
	 * 
	 * @throws	\BadMethodCallException		if no options have been set
	 */
	public function getOptions() {
		return $this->__options;
	}
	
	/**
	 * Sets the possible options of this selection and returns this field.
	 * 
	 * @param	array|callable		$options	selectable options or callable returning the options
	 * @return	static					this field
	 * 
	 * @throws	\InvalidArgumentException		if given options are no array or callable or otherwise invalid
	 * @throws	\UnexpectedValueException		if callable does not return an array
	 */
	public function options($options) {
		if (!is_array($options) && !is_callable($options)) {
			throw new \InvalidArgumentException("Given options are neither an array nor a callable, " . gettype($options) . " given.");
		}
		
		if (is_callable($options)) {
			$options = $options();
			
			if (!is_array($options)) {
				throw new \UnexpectedValueException("The options callable is expected to return an array, " . gettype($options) . " returned.");
			}
		}
		
		// validate options and read possible values
		$validateOptions = function($array) use (&$validateOptions) {
			foreach ($array as $key => $value) {
				if (is_array($value)) {
					$validateOptions($value);
				}
				else {
					if (!is_string($value) && !is_numeric($value)) {
						throw new \InvalidArgumentException("Options contain invalid label of type " . gettype($value) . ".");
					}
					
					if (in_array($key, $this->possibleValues)) {
						throw new \InvalidArgumentException("Options values must be unique, but '" . $key . "' appears at least twice as value.");
					}
					
					$this->possibleValues[] = $key;
				}
			}
		};
		
		$validateOptions($options);
		
		$this->__options = $options;
		
		return $this;
	}
	
	/**
	 * @inheritDoc
	 */
	public function readValue() {
		if (isset($_POST[$this->getPrefixedId()]) && is_string($_POST[$this->getPrefixedId()])) {
			$this->__value = $_POST[$this->getPrefixedId()];
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function validate() {
		if (!in_array($this->getValue(), $this->possibleValues)) {
			$this->addValidationError(new FormFieldValidationError('invalidValue', 'wcf.global.form.selection.error.invalidValue'));
		}
		
		parent::validate();
	}
}