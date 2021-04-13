# Prisma Decidir Magento 2

## M2 Versiones soportadas
* Magento 2.4+

## Instalación
### Descarga
1. Descargar módulo desde
    - [github](https://github.com/decidir/dec_magento)
2. Crear el siguiente path/directorio `app/code/Prisma/Decidir/`
3. Descomprimir y copiar el contenido del module en `app/code/Prisma/Decidir/`
4. Ejecutar `bin/magento module:enable Prisma_Decidir --clear-static-content`
5. Ejecutar `bin/magento setup:di:compile`
6. Ejecutar `bin/magento setup:static-content:deploy`
7. Ejecutar `bin/magento cache:clean`

## Configuración
### Credenciales Sandbox
Si las siguientes credenciales no funcionan, por favor referirse a la  [official documentation](https://decidirv2.api-docs.io/1.0/transacciones-simples/flujo-de-una-transaccion-simple) en caso que hayan cambiado.

| Tipo        | Valor                            |
|:------------|:---------------------------------|
| Site Id     | 28464383                         |
| Public Key  | e9cdb99fff374b5f91da4480c8dca741 |
| Private Key | 92b71cf711ca41f78362a7134f87ff65 |

## Desinstalación
1. Ejecutar `bin/magento module:disable Prisma_Decidir --clear-static-content`
2. Remover path/directorio `Prisma/Decidir/` dentro de `app/code`
