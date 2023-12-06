import os

BAN_LIST_FILE = "ban_list.txt"
MAX_ATTEMPTS = 3

def main():
    # Saisie du nom d'utilisateur et du mot de passe
    username = input("Nom d'utilisateur : ")
    password = input("Mot de passe : ")

    # si l'adresse IP est bannie
    if is_ip_banned(get_ip()):
        print("IP bannie. Connexion refusée.")
    else:
        # Si l'adresse IP n'est pas bannie, tente l'authentification
        if authenticate(username, password):
            print("Authentification réussie.")
        else:
            print("Authentification échouée.")
            # la tentative échouée
            handle_failed_attempt()

def authenticate(username, password):
    # ici j'imagine que je fais une requête à une base de données pour vérifier les informations d'authentification
    # Je returne faut pour les bien du test
    return False

def handle_failed_attempt():
    ip = get_ip()

    # Combien de nombre de tentatives précédentes
    attempts = get_attempts(ip) + 1

    # j'enregistre le nombre de tentatives
    save_attempts(ip, attempts)

    # Si le nombre de tentatives dépasse le seuil maximal je ban l'IP
    if attempts >= MAX_ATTEMPTS:
        print("Trop de tentatives échouées. Bannissement de l'IP : " + ip)
        ban_ip(ip)

def ban_ip(ip):
    # Ajoute l'IP bannie au fichier
    with open(BAN_LIST_FILE, "a") as file:
        file.write(ip + "\n")

def is_ip_banned(ip):
    try:
        # je lis le fichier des IP bannies et vérifie si mon IP est dedans
        with open(BAN_LIST_FILE, "r") as file:
            return ip in file.read().splitlines()
    except FileNotFoundError:
        # Si le fichier n'existe pas, l'IP n'est pas bannie
        return False

def get_attempts(ip):
    # Récupère le nombre de tentatives depuis la base de données
    return 0

def save_attempts(ip, attempts):
    # ici je devrais enregistrer le nombre de tentatives dans une base de données
    pass

def get_ip():
    # ici je récupère l'adresse IP du client
    # pour les tests je retourne une IP fictive
    return "192.168.0.1"

if __name__ == "__main__":
    main()
