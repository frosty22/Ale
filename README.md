ALE
===

Základ valné většiny modulů / komponent a knihoven.



Základní entity
---------------

Knihovna obsahuje 3 základní entity (BaseEntity, IdentifiedEntity, NamedEntity), od BaseEntity by měli dědit všechny entity aplikace (IdentifiedEntity a NamedEntity dědí též) a jedná se o základní entitu, která zapouzdřuje funkčnost z Kdyby/Doctrine, a používá se při práci s ostatními knihovnami (například DataExt, EntityMetaReader, a další).


IPresenterProvider
------------------

Rozhraní IPresenterProvider mohou dědit rozšíření DI, a slouží k mapování presenterů, které mohou obsahovat jednotlivé moduly - viz inspirace http://forum.nette.org/en/1193-extending-extensions-solid-modular-concept

Z důvodu kompatibility se stable verzí Nette, která v tuto chvíli nepodporuje mapování presenterů, je zde obsažen i vlastní PresenterFactory, který je upravenou kopií z dev verze Nette.
