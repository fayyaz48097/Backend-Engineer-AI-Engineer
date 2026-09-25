import sys
import calculator

# print(sys.path)

# for path in sys.path:
#     print(path)



print("Python executable:")
print(sys.executable)

print("\nSearch paths:")
for path in sys.path:
    print(path)

print("\nIs calculator loaded?")
print("calculator" in sys.modules)

