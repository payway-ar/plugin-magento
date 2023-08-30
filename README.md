# Payway Payment for Magento 2

## M2 Versiones soportadas
* Magento 2.4+

## Payway PHP-SDK version soportada
* 1.5.6

## Aclaración: 
**Magento solo soporta la modalidad de pago Simple y Simple con Cybersource- Retail**

## Instalación
### Composer
1. Ejecutar `composer require "prisma/module-payway"`
   - Instalará los modulos `Prisma_Payway` y `Prisma_PaywayPromotions`
2. Ejecutar `bin/magento module:enable Prisma_Payway`
3. Ejecutar `bin/magento module:enable Prisma_PaywayPromotions`
4. Ejecutar `bin/magento setup:upgrade`
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
| Region Source          | Magento Default <br> MugAr ArgentinaRegions Module | Magento Default|  


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
    - Se obtendrá el código, cuyo formato es `AR-C`, y se procesará en la clase `Prisma\Payway\Model\Utility\RegionHandler`, el method `parseMugarRegionCode($code)` retornará el valor procesado necesario para el Gateway.

## Configuración Promociones

- Bancos
    - Promociones de Payway -> Admimistrar Bancos
      Aqui se darán de alta los Bancos pudiendo ingresar
        - Habilitar Banco (siempre debe estar habilitado)
        - Nombre del Banco
        - Logo
- Tarjetas de Crédito
    - Promociones de Payway -> Admimistrar Tarjetas de Crédito
      Aqui se darán de alta las Tarjetas de Crédito pudiendo ingresar
        - Habilitar Tarjeta (siempre debe estar habilitada)
        - Nombre de la Tarjeta
        - ID SPS -> ID de pago a enviar a Payway
        - ID NPS -> no utilizado actualmente
        - Logo
- Promociones / Planes de Cuotas
    - Promociones de Payway -> Admimistrar Promociones
      Aqui se darán de alta las Promociones o Planes de Pago,
      combinando Banco/Tarjeta,  pudiendo ingresar
        - Habilitar Promoción
        - Nombre de la Regla
        - Tarjeta a la que aplicará
        - Fecha de inicio de vigenta de la promoción
        - Banco al que aplicará
        - Prioridad
        - Fecha de fin de vigenta de la promoción
        - Días en los cuales estará vigente
        - Websites a los que aplica
        - Planes De cuotas
            - Cuota -> Valor mostrado en front y utilizado para calcular el monto de la misma
            - Coeficiente -> Intereses aplicables
            - TEA -> Valor informativo Informativo
            - CFT -> Valor informativo Informativo
            - Cuota que se Envia -> Valor de la cuota utilizado para enviar a Payway

## Transaction Logs
#### Save transaction logs 
  Si se habilita esta opción, se guardara en la tabla `prisma_payway_transaction_logs` el payload de la respuesta de Payway e info relevante por cada transacción realizada.
#### Enable Cleanup Logs Cron
  Si se habilita esta opción, se ejecutara un cronjob que eliminará registros de la tabla `prisma_payway_transaction_logs` de acuerdo a los dias configurados. <br>
  ###### Importante:
**_Si se habilita el guardado de logs es importante y recomendable habilitar este cron para limpiar registros y prevenir un oversize de la tabla_**
#### Clean Transaction Logs Older Than
  Esta opción permite seleccionar la antigüedad de los logs a mantener en la tabla mecionada en el punto anterior.

---
## Deshabilitar Módulos
1. Ejecutar `bin/magento module:enable Prisma_PaywayPromotions`
2. Ejecutar `bin/magento module:disable Prisma_Payway`
3. Ejecutar `bin/magento setup:upgrade`
4. Ejecutar `bin/magento setup:di:compile`
5. Ejecutar `bin/magento setup:static-content:deploy`
6. Ejecutar `bin/magento cache:flush`

## Desinstalación
1. Ejecutar `composer remove "prisma/module-payway"`
   
*NOTA:*
 
No se removerán tablas:
- `prisma_payway_promotions_bank`
- `prisma_payway_promotions_card`
- `prisma_payway_promotions_rules`
- `prisma_payway_transaction_logs`

No se removerán los datos de:
- `core_config_data`
