#!/bin/bash

# Cores para output
GREEN='\033[0;32m'
NC='\033[0m' # No Color

echo -e "${GREEN}Instalando o Sistema de Gerenciamento de Tarefas...${NC}"

# Copia o arquivo .env de exemplo se não existir
if [ ! -f ".env" ]; then
    echo -e "${GREEN}Criando arquivo .env${NC}"
    cp .env.example .env
fi

# Inicia os containers Docker
echo -e "${GREEN}Iniciando os containers Docker...${NC}"
docker-compose up -d

# Instala dependências do Composer
echo -e "${GREEN}Instalando dependências do Composer...${NC}"
docker-compose exec app composer install

# Gera a chave da aplicação
echo -e "${GREEN}Gerando chave da aplicação...${NC}"
docker-compose exec app php artisan key:generate

# Executa as migrações e seeders
echo -e "${GREEN}Executando migrações e seeders...${NC}"
docker-compose exec app php artisan migrate --seed

# Limpa o cache
echo -e "${GREEN}Limpando cache...${NC}"
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan view:clear

echo -e "${GREEN}Ajustando permissões...${NC}"
docker-compose exec app chmod -R 775 storage bootstrap/cache

echo -e "${GREEN}Instalação concluída!${NC}"