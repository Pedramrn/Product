<?php namespace Jiro\Product\Database\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jiro\Property\PropertySubjectInterface;
use Jiro\Variation\VariableInterface;
use Jiro\Product\PropertyValueInterface;
use Jiro\Product\VariationInterface;
use Jiro\Product\OptionInterface;
use Jiro\Product\ProductInterface;

/**
 * Catalog product model.
 *
 * @author Andrew McLagan <andrewmclagan@gmail.com>
 */

class Product extends Model implements ProductInterface, VariableInterface, PropertySubjectInterface
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'products';

    /**
     * White list of fillable attributes.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'slug', 
        'description', 
        'meta_keywords',
        'meta_description',
        'available_on'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // TODO: how about we just use laravel defaults in the migration file
        if (!array_key_exists('available_on', $attributes))
        {
            $this->setAvailableOn(new \Carbon\Carbon);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * {@inheritdoc}
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaKeywords()
    {
        return $this->meta_keywords;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaKeywords($meta_keywords)
    {
        $this->meta_keywords = $meta_keywords;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaDescription()
    {
        return $this->meta_description;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaDescription($meta_description)
    {
        $this->meta_description = $meta_description;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
        return new \DateTime() >= $this->available_on;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableOn()
    {
        return $this->available_on;
    }

    /**
     * {@inheritdoc}
     */
    public function setAvailableOn(\DateTime $available_on = null)
    {
        $this->available_on = $available_on;

        return $this;
    }

    /** 
     * {@inheritdoc}
     */
    public function properties()
    {
        return $this->hasMany('Jiro\Product\Property\Eloquent\PropertyValue','product_id');
    }

    /** 
     * {@inheritdoc}
     */
    public function setProperties($properties)
    {
        $this->properties()->saveMany($properties->all());

        return $this;        
    }

    /** 
     * {@inheritdoc}
     */
    public function addProperty(PropertyValueInterface $property)
    {             
        $this->properties()->save($property);

        return $this;        
    }

    /** 
     * {@inheritdoc}
     */
    public function removeProperty(PropertyValueInterface $property)
    {
        $property->delete();

        return $this;        
    }

    /** 
     * {@inheritdoc}
     */
    public function hasProperty(PropertyValueInterface $property)
    {
        return $this->properties->contains($property);        
    }

    /** 
     * {@inheritdoc}
     */
    public function hasPropertyByName($propertyName)
    {
        foreach ($this->properties as $property) 
        {
            if($property->getName() === $propertyName)
            {
                return true;
            }
        }

        return false;        
    }

    /** 
     * {@inheritdoc}
     */
    public function getPropertyByName($propertyName)
    {
        foreach ($this->properties as $property) 
        {
            if ($property->getName() === $propertyName) 
            {
                return $property;
            }
        }

        return null;
    }  

    /**
     * {@inheritdoc}
     */
    public function getMasterVariation()
    {
        foreach ($this->variations as $variation) 
        {
            if ($variation->isMaster()) 
            {
                return $variation;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function setMasterVariation(VariationInterface $masterVariation)
    {
        $masterVariation->setMaster(true);

        $this->variations()->save($masterVariation);

        return $this;        
    }

    /**
     * {@inheritdoc}
     */
    public function variations()
    {
        return $this->hasMany('Jiro\Product\Variation\Eloquent\Variation', 'product_id');
    }

    /**
     * {@inheritdoc}
     */
    public function setVariations($variations)
    {
        $this->variations()->saveMany($variations);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addVariation(VariationInterface $variation)
    {
        $this->variations()->save($variation);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeVariation(VariationInterface $variation)
    {
        $variation->delete();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasVariation(VariationInterface $variation)
    {
        return $this->variations->contains($variation);
    }

    /**
     * {@inheritdoc}
     */
    public function options()
    {
        return $this->belongsToMany(
            'Jiro\Product\Option\Eloquent\Option',
            'option_product',
            'product_id',
            'option_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions($options)
    {
        $this->options()->sync($options);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addOption(OptionInterface $option)
    {
        $this->options()->attach($option);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeOption(OptionInterface $option)
    {
        $option->delete();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasOption(OptionInterface $option)
    {
        return $this->options->contains($option);
    }    

    /**
     * {@inheritdoc}
     */
    public function hasOptions()
    {
        return (boolean) $this->options->count();
    }
}
