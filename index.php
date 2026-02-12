<?php

class Category {
    private string $id;
    private string $name;
    private string $description;

    public function __construct(string $name, string $description = "")
    {
        $this->id = uniqid();
        $this->name = $name;
        $this->description = $description;
    }

    public function getId(): string { return $this->id; }
    public function getName(): string { return $this->name; }

    public function __toString(): string {
        return "Catégorie [{$this->name}] (ID: {$this->id}) : {$this->description}";
    }
}

class Product {
    private string $id;
    private string $name;
    private float $price;
    private int $stock;
    private Category $category; 

    public function __construct(
        string $name,
        float $price,
        Category $category,
        int $stock
    ) {
        $this->id = uniqid();
        $this->name = $name;
        $this->price = $price;
        $this->category = $category;
        $this->stock = $stock;
    }

    public function getId(): string { return $this->id; }
    public function getPrice(): float { return $this->price; }
    public function getStock(): int { return $this->stock; }

    public function getName(): string { return $this->name; }

    public function getCategoryName(): string {
        return $this->category->getName();
    }

    public function isAvailable(): bool {
        return $this->stock > 0;
    }

    public function __toString(): string {
        $dispo = $this->isAvailable() ? "Oui" : "Non";
        return "Produit <strong>{$this->name}</strong> <br>" .
               "Prix : {$this->price} € <br>" .
               "Disponible : {$dispo} <br>" .
               "Stock : {$this->stock} <br>" .
               "Appartient à : " . $this->category->getName();
    }
}

class Cart {
    private array $items = [];

    public function addProduct(Product $product, int $quantity = 1): void {
        $id = $product->getId();

        $currentQty = isset($this->items[$id]) ? $this->items[$id]['quantity'] : 0;
        $totalRequested = $currentQty + $quantity;

        if ($totalRequested > $product->getStock()) {
            echo "<span style='color:red'>Erreur : Stock insuffisant pour '{$product->getName()}' ! (Stock: {$product->getStock()}, Demandé: $totalRequested)</span><br>";
            return;
        }

        if (isset($this->items[$id])) {
            $this->items[$id]['quantity'] = $totalRequested;
        } else {
            $this->items[$id] = [
                'product' => $product,
                'quantity' => $quantity
            ];
        }
        
        echo "Ajouté au panier : {$quantity} x {$product->getName()}<br>";
    }

    public function removeProduct(Product $product): void {
        $id = $product->getId();
        if (isset($this->items[$id])) {
            unset($this->items[$id]);
            echo "Retiré du panier : {$product->getName()}<br>";
        }
    }

    public function getTotal(): float {
        $total = 0.0;
        foreach ($this->items as $item) {
            $total += $item['product']->getPrice() * $item['quantity'];
        }
        return $total;
    }

        public function __toString(): string {
        if (empty($this->items)) {
            return "Your cart is empty.<br>";
        }

        $output = "<strong>--- Cart Details ---</strong><br>";

        foreach ($this->items as $item) {
            $p = $item['product'];
            $q = $item['quantity'];
            $subtotal = $p->getPrice() * $q;

            $output .= "• " . $p->getName() . " (x$q) : " . $subtotal . " €<br>";
        }

        $output .= "-------------------------<br>";
        $output .= "<strong>TOTAL: " . $this->getTotal() . " €</strong><br>";

        return $output;
    }

}

// TEST

echo "<h1>Simulation Gobline's Bazar</h1>";

$catArmes = new Category("Armes", "Offensif");
$catPotions = new Category("Potions", "Consommable");

$epee = new Product("Épée en Fer", 100.0, $catArmes, 5);
$potion = new Product("Potion de Vie", 50.0, $catPotions, 10);
$bouclier = new Product("Bouclier Rond", 80.0, $catArmes, 2);

echo "<h3>1. Affichage des produits disponibles</h3>";
echo $epee . "<br><br>" . $potion;

echo "<h3>2. Actions Client</h3>";
$panier = new Cart();

$panier->addProduct($epee, 1);
$panier->addProduct($potion, 3);

$panier->addProduct($epee, 1);

$panier->addProduct($bouclier, 10); 

echo "<h3>3. Contenu du Panier</h3>";
echo $panier;
