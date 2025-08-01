<?php

namespace App\Http\Livewire;

// Add any other models you need, like Floor, Condition, etc.

use App\Models\Amenity\Amenity;
use App\Models\Category\Category;
use App\Models\City\City;
use App\Models\Floor\Floor;
use App\Models\Orientation\Orientation;
use App\Models\Property\Property;
use App\Models\PropertyCondition\PropertyCondition;
use App\Models\Type\Type;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class PropertySearch extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // --- FILTERS from original file ---
    public $type = '';
    public $governorate = '';
    public $category = '';
    public $price_min;
    public $price_max;
    public $area_min;
    public $area_max;
    public $rooms_count;
    public $floor = '';
    public $orientation = '';
    public $condition = '';
    public array $amenities = [];
    public array $attributeValues = [];

    // --- MAP BOUNDS ---
    public $latMin;
    public $latMax;
    public $lngMin;
    public $lngMax;
    private bool $isMapSearch = false; // Internal flag to control smart zooming

    // --- UI & SORTING ---
    public $layout = 'row'; // Default from original file
    public $sortKey = ''; // Default from original file

    // Replicates the URL syncing from the original file automatically
    protected $queryString = [
        'type' => ['except' => ''],
        'governorate' => ['except' => ''],
        'category' => ['except' => ''],
        'price_min' => ['except' => ''],
        'price_max' => ['except' => ''],
        'area_min' => ['except' => ''],
        'area_max' => ['except' => ''],
        'rooms_count' => ['except' => ''],
        'floor' => ['except' => ''],
        'orientation' => ['except' => ''],
        'condition' => ['except' => ''],
        'amenities' => ['except' => []],
        'sortKey' => ['except' => ''],
        'latMin' => ['except' => ''],
        'latMax' => ['except' => ''],
        'lngMin' => ['except' => ''],
        'lngMax' => ['except' => ''],
        'layout' => ['except' => 'row'],
    ];

    public function updatedLayout($value)
    {
        // We dispatch an event so the Alpine component in the layout file can be notified of the change.
        $this->dispatch('layoutChanged', layout: $value);
    }

    // Listen for the event dispatched by Alpine when the map is moved
    #[On('mapBoundsChanged')]
    public function updateMapBounds($bounds)
    {
        $this->isMapSearch = true; // Set flag to prevent map from re-fitting
        $this->latMin = $bounds['south'];
        $this->latMax = $bounds['north'];
        $this->lngMin = $bounds['west'];
        $this->lngMax = $bounds['east'];
        $this->resetPage(); // Reset pagination on map search
    }

    // This runs before any property is updated.
    public function updated($property)
    {
        // Reset pagination if any filter is changed
        if ($property !== 'page') {
            $this->resetPage();
        }
    }

    public function render()
    {
        $query = Property::query()->with(['images', 'city']);

        // --- Apply all filters ---
        $query->when($this->type, fn($q) => $q->where('type_id', $this->type));
        $query->when($this->governorate, fn($q) => $q->whereHas('city', fn($q2) => $q2->where('name', 'LIKE', "%{$this->governorate}%")));
        $query->when($this->category, fn($q) => $q->where('category_id', $this->category));
        $query->when($this->price_min, fn($q) => $q->where('price', '>=', $this->price_min));
        $query->when($this->price_max, fn($q) => $q->where('price', '<=', $this->price_max));
        $query->when($this->area_min, fn($q) => $q->where('size', '>=', $this->area_min));
        $query->when($this->area_max, fn($q) => $q->where('size', '<=', $this->area_max));
        $query->when($this->rooms_count, fn($q) => $q->where('rooms_count', $this->rooms_count));
        $query->when($this->floor, fn($q) => $q->where('floor_id', $this->floor));
        $query->when($this->orientation, fn($q) => $q->where('orientation_id', $this->orientation));
        $query->when($this->condition, fn($q) => $q->where('condition_id', $this->condition));

        if (!empty($this->amenities)) {
            $query->whereHas('amenities', fn($q) => $q->whereIn('amenity_id', $this->amenities), '>=', count($this->amenities));
        }

        // Dynamic Attributes filter (assuming relationship is set up)
        foreach ($this->attributeValues as $attributeId => $valueId) {
            if ($valueId) {
                $query->whereHas('attributes', function ($q) use ($attributeId, $valueId) {
                    $q->where('attribute_id', $attributeId)->where('custom_attribute_value_id', $valueId);
                });
            }
        }

        // Apply map bounds filter if they exist
        $query->when($this->latMin, fn($q) => $q->whereBetween('latitude', [$this->latMin, $this->latMax]));
        $query->when($this->lngMin, fn($q) => $q->whereBetween('longitude', [$this->lngMin, $this->lngMax]));

        // --- Apply Sorting ---
        if ($this->sortKey) {
            $parts = explode('_', $this->sortKey);
            $column = $parts[0];
            $direction = $parts[1] === 'asc' ? 'asc' : 'desc';
            if (in_array($column, ['price', 'size', 'rooms_count', 'created_at'])) {
                $query->orderBy($column, $direction);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $properties = $query->paginate(15);

        // Send results to Alpine, including the smart-zoom flag
        $this->dispatch('propertiesUpdated', [
            'properties' => $properties->items(),
            'fitBounds' => !$this->isMapSearch
        ]);

        $this->isMapSearch = false; // Reset flag after render

        return view('livewire.property-search', [
            'properties' => $properties,
            'types' => Type::all(),
            'governorates' => City::all(), // Or however you get your governorates
            'categories' => Category::all(),
            'floors' => Floor::all(),
            'conditions' => PropertyCondition::all(),
            'orientations' => Orientation::all(),
            'allAmenities' => Amenity::all(),
            'propertyAttributes' => $this->category ? Category::find($this->category)?->attributes()->with('customAttributeValues')->get() : [],
        ]);
    }
}
