ALE
===

Základ valné většiny modulů / komponent a knihoven.


Instalace
---------

1. Stažení přes composer
2. Připojení DI rozšíření Ale\DI\AleExtension

> Pokud nevíte jakým způsobem připojit rošíření, dopoučuji použít rošíření https://github.com/vojtech-dobes/nette-extensions-list, které umožňuje následně v konfiguračním souboru definovat sekci "extensions", kde pak lze jednoduše přidat toto rošíření. Alternativně je nutné v boostrapu v události onCompile na Configuration zavěsit callback, který bude přidávat všechna Vaše rožšíření pomocí volání metody addExtension na Compiler.


Základní entity
---------------

Knihovna obsahuje 3 základní entity (BaseEntity, IdentifiedEntity, NamedEntity), od BaseEntity by měli dědit všechny entity aplikace (IdentifiedEntity a NamedEntity dědí též) a jedná se o základní entitu, která zapouzdřuje funkčnost z Kdyby/Doctrine, a používá se při práci s ostatními knihovnami (například DataExt, EntityMetaReader, a další).


IPresenterProvider
------------------

Rozhraní IPresenterProvider mohou dědit rozšíření DI, a slouží k mapování presenterů, které mohou obsahovat jednotlivé moduly - viz inspirace http://forum.nette.org/en/1193-extending-extensions-solid-modular-concept

Z důvodu kompatibility se stable verzí Nette, která v tuto chvíli nepodporuje mapování presenterů, je zde obsažen i vlastní PresenterFactory, který je upravenou kopií z dev verze Nette.
