# class AgeTooYoungError(Exception):
#     pass


# age = 15

# if age < 18:
#     raise AgeTooYoungError("You must be 18 or older.")


class InvalidAgeError(Exception):
    pass

class InvalidNameError(Exception):
    pass


try:
    age = int(input("Enter your age: "))
    name = str(input("Enter your name: "))

    if age < 18 or age > 150:
        raise InvalidAgeError("You must be 18 or under 150.")
    
    if len(name) < 3:
        raise InvalidNameError("Your name must be greater than 3 characters")
    
    print("You can continue.")

except InvalidAgeError as error:
    print(f"Error: {error}")

except InvalidNameError as error:
    print(f"Error: {error}")
else:
    print(f"Welcome {name}! You are {age} years old.")

# class InsufficientBalanceError(Exception):

#     def __init__(self, balance, amount):
#         self.balance = balance
#         self.amount = amount

#         super().__init__(
#             f"Balance is {balance}, but {amount} is required."
#         )

# balance = 500
# amount = 1000

# try: 
#     if amount > balance:
#      raise InsufficientBalanceError(balance, amount)
# except InsufficientBalanceError as error:
#     print(error)
