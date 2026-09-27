
# try:
#     age = int(input("enter your age: "))
#     print(age)

# except:    print("went something wrong: maybe type error.")


# try:
#     age = int(input("Age: "))
# except ValueError:
#     print("Please enter a valid number.")


# try:

#     age = int(input("Age: "))
#     print(age/0)

# except ZeroDivisionError:
#     print("bla bla")


# ----------Challenge----------------#

# try:
#     age = int(input("Enter your age: "))
#     print(f"You are {age} years old.")

# except ValueError:
#     print("Please enter a valid number.")




# numbers = [10, 20, 30]

# try:
#     print(numbers[10])
# except IndexError:
#     print("That index does not exist.")


# tes only
# try:
#     print(numbers[10])
# except:
#     print("That index does not exist.")

# numbers = [10, 20, 30]

# try:
#     print(numbers[1])
# except IndexError:
#     print("Index error")

# print("Program continues")


# --------------exception object --------------


# try:
#     number = int("hello")

# except ValueError as error:
#     print("Something went wrong:")
#     print(error)

# try:
#     numbers = [10, 20, 30]
#     print(numbers[10])

# except IndexError as error:
#     print("Something went wrong!")
#     print(error)


# --------------multiple exception--------------

# try:
#     number = int(input("Enter a number: "))
#     result = 100 / number
#     print(result)

# except ValueError as error:
#     print("Please enter a valid number.")
#     print(error)

# except ZeroDivisionError as error:
#     print("You cannot divide by zero.")
#     print(error)


# --------------with else exception--------------

# try:
#     number =  int(input("Enter a number: "))
# except ValueError:
#     print("Invalid number.")
# else:
#     print(f"Number is {number}.") # if try success then run.

# try:
#     number = int(input("Enter a number: "))
#     result = 100 / number
# except ValueError:
#     print("Invalid number.")
# except ZeroDivisionError:
#     print("Divide by zero.")
# else:
#     print(f"Result: {result}")


# try:
#     number = int("hello")
# except ValueError:
#     print("Invalid number.")
# else:
#     print("Conversion successful.")
# finally:
#     print("This always runs.")

# age = -5

# if age < 0:
#     raise ValueError("Age cannot be negative.")

# print("Valid age")




try:
   age = int(input("enter you age: ")) 
 
   if age < 0:
        raise ValueError("Age cannot be negative.")
   print("Valid age")
   
except ValueError as error:
    print(f"Error: {error}")

else:
     print(f"your age is {age};")
finally: 
    print("Your young :)")