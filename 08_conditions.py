age = 24

if age >= 18:
    print("Adult")

    # if (age >= 18) :
    #     print("Adult")

age = 16

if age >= 18:
    print("Adult")
else:
    print("Minor")



score = 75

if score >= 90:
    print("A")
elif score >= 80:
    print("B")
elif score >= 70:
    print("C")
else:
    print("Fail")



age = 24
is_developer = True

if age >= 18 and is_developer:
    print("Access granted")


    role = "developer"

if role == "admin" or role == "developer":
    print("Allowed")


    is_banned = False

if not is_banned:
    print("User can continue")


    age = 24
is_logged_in = True


if is_logged_in:
    if age >= 18:
        print("Access granted")



if is_logged_in and age >= 18:
    print("Access granted")