# Prisma Decidir Magento 2

## M2 Versiones soportadas
* Magento 2.4+

## Decidir PHP-SDK version soportada
* 1.5.6

## Instalación
### Descarga
1. Descargar módulo desde
    - [github](https://github.com/decidir/dec_magento)
2. Crear el siguiente path/directorio `app/code/Prisma/Decidir/`
3. Descomprimir y copiar el contenido del module en `app/code/Prisma/Decidir/`
4. Instalar `decidir2/php-sdk` ejecutando `composer require decidir2/php-sdk:1.5.6`
4. Ejecutar `bin/magento module:enable Prisma_Decidir --clear-static-content`
5. Ejecutar `bin/magento setup:di:compile`
6. Ejecutar `bin/magento setup:static-content:deploy`
7. Ejecutar `bin/magento cache:clean`

## Configuración
### Credenciales Sandbox
Si las siguientes credenciales no funcionan, por favor referirse a la [official documentation](https://decidirv2.api-docs.io/1.0/transacciones-simples/flujo-de-una-transaccion-simple) en caso que hayan cambiado.

| Tipo        | Valor                            |  
|:------------|:---------------------------------|  
| Site Id     | 28464383                         |  
| Public Key  | e9cdb99fff374b5f91da4480c8dca741 |  
| Private Key | 92b71cf711ca41f78362a7134f87ff65 |  

### Cybersource
| Opción                 | Valor        | Default             |  
|:-----------------------|:-------------|:--------------------|  
| Enable/disable         | Yes/No       | Yes                 |  
| Vertical               | Retail       | Retail              |  
| Region Source          | Magento Default <br> MugAr ArgentinaRegions Module <br> Custom Regions | Magento Default|  


#### Regiones Argentinas
El Gateway de pago necesita valores particulares para identificar las regiones/provincias Argentinas
Las regiones/provincias deben ser un campo requerido.

El módulo brinda las siguientes opciones de compatibilidad:
- **Magento Default**: Se utilizará el código obtenido de la tabla `directory_country_region`. <br>  
  Para validar la existencia de los datos necesarios ejecute el siguiente query en la base de datos y compare el resultado con la [Tabla de Referencia](https://decidirv2.api-docs.io/1.0/tablas-de-referencia-e-informacion-para-el-implementador/zpLTePd4PeuPdDBHN)
 ```  
 SELECT * FROM directory_country_region AS dcr WHERE dcr.country_id = "AR";    
  ```  
Para insertar datos puede ejecutar el siguiente query

  ```  
  INSERT INTO directory_country_region (country_id, code, default_name)   
  VALUES  
 ("AR", "C", "Ciudad Autónoma De Buenos Aires")
  ("AR", "B", "Buenos Aires"),
  ("AR", "K", "Catamarca"),
  ("AR", "H", "Chaco"),
  ("AR", "U", "Chubut"),
  ("AR", "X", "Córdoba"),
  ("AR", "W", "Corrientes"),
  ("AR", "E", "Entre Ríos"),
  ("AR", "P", "Formosa"),
  ("AR", "Y", "Jujuy"),
  ("AR", "L", "La Pampa"),
  ("AR", "F", "La Rioja"),
  ("AR", "M", "Mendoza"),
  ("AR", "N", "Misiones"),
  ("AR", "Q", "Neuquén"),
  ("AR", "R", "Río Negro"),
  ("AR", "A", "Salta"),
  ("AR", "J", "San Juan"),
  ("AR", "D", "San Luis"),
  ("AR", "Z", "Santa Cruz"),
  ("AR", "S", "Santa Fe"),
  ("AR", "G", "Santiago del Estero"),
  ("AR", "V", "Tierra del Fuego"),
  ("AR", "T", "Tucumán")
  ;
   ``` 
*NOTA:*   
Si desea tener las traducciones para las Provincias debe insertar en la tabla `directory_country_region_name` los valores necesarios, por ejemplo:
 ```
	INSERT INTO directory_country_region_name values ("en_US", 888, "Buenos Aires");   
```  
Donde `en_US` es el locale, `888` es el region_id (este value se obtiene ejecutando el query (SELECT ...) del primer paso luego del INSERT), y `Buenos Aires` el nombre. <br>


- **MugAr ArgentinaRegions**: <br>  
  Si no tiene las regiones Argentinas en su Magento, recomendamos su instalación [module-argentina-regions](https://github.com/holamugar/module-argentina-regions).
    - Este módulo inyecta todos los datos necesarios para las regiones Argentinas  en la tabla `directory_country_region`. <br>
    - Este módulo inyecta todos los datos necesarios para las regiones Argentinas  en la tabla `directory_country_region`. <br>
    - Se obtendrá el código, cuyo formato es `AR-C`, y se procesará en la clase `Prisma\Decidir\Model\Utility\RegionHandler`, el method `parseMugarRegionCode($code)` retornará el valor procesado necesario para el Gateway.

## Desinstalación
1. Ejecutar `bin/magento module:disable Prisma_Decidir --clear-static-content`
2. Remover path/directorio `Prisma/Decidir/` dentro de `app/code`
